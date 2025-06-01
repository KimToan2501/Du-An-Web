<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<section class="admin-form-wrapper">
    <form id="form-add" class="admin-form">
        <div class="admin-form__group">
            <label for="scheduleDate" class="admin-form__label">Ngày làm việc (*)</label>
            <input type="date" id="scheduleDate" disabled name="date" class="admin-form__input" required value="<?= $date ?>">
        </div>

        <div class="admin-form__group">
            <label class="admin-form__label">Chọn ca làm việc (*)</label>
            <div class="time-slot-selection">
                <?php foreach ($timeSlots as $timeSlot) : ?>
                    <button type="button" class="btn btn--secondary time-slot-btn" data-id="<?= $timeSlot->time_slot_id ?>">
                        <?= $timeSlot->start_time ?> - <?= $timeSlot->end_time ?>
                    </button>
                <?php endforeach; ?>
                <input type="hidden" id="timeSlotIds" name="time_slot_ids" required>
            </div>
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="btn btn--primary">Lưu thay đổi</button>
            <a href="<?= base_url('/admin/staff/schedule/' . $staff->user_id) ?>" class="btn btn--gray ml-2">Hủy</a>
        </div>
    </form>
</section>

<?php start_section('scripts') ?>
<script>
    $(document).ready(function() {
        let initialData = <?= json_encode($initialData) ?>;
        let selectedTimeSlotIds = [];
        let currentScheduleData = []; // Track current state of schedule data

        // Initialize data structure
        if (initialData?.length > 0) {
            initialData = initialData.map(item => ({
                ...item,
                outdated: false,
                isInitiallySelected: true // Mark as initially selected
            }));

            // Create a copy for tracking current state
            currentScheduleData = JSON.parse(JSON.stringify(initialData));
        }

        const selectedDate = "<?= $date ?>";
        const today = new Date().toISOString().slice(0, 10);
        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();

        console.log('Initial Data:', initialData);

        // Initialize time slot buttons with proper states
        function initializeTimeSlotButtons() {
            $('.time-slot-btn').each(function() {
                const timeSlotId = $(this).data('id');
                const existingSchedule = initialData.find(item => item.time_slot_id === timeSlotId);

                // Reset button classes
                $(this).removeClass('btn--primary btn--secondary disabled booked outdated-checked')
                    .addClass('btn--secondary')
                    .prop('disabled', false);

                if (existingSchedule) {
                    const timeSlotStartTime = existingSchedule?.time_slot?.start_time?.split(' - ')[0] ||
                        $(this).text().split(' - ')[0];
                    const [slotHour, slotMinute] = timeSlotStartTime.split(':').map(Number);

                    // Create DateTime objects for comparison
                    const selectedDateTime = new Date(selectedDate + 'T' + String(slotHour).padStart(2, '0') + ':' + String(slotMinute).padStart(2, '0') + ':00');
                    const currentDateTime = new Date();

                    // Check if time slot is outdated (selected datetime <= current datetime)
                    if (selectedDateTime <= currentDateTime) {
                        existingSchedule.outdated = true;
                        $(this).addClass('disabled outdated-checked').prop('disabled', true);
                    }

                    // Add to selected array
                    if (!selectedTimeSlotIds.includes(existingSchedule.time_slot_id)) {
                        selectedTimeSlotIds.push(existingSchedule.time_slot_id);
                    }

                    // Set button state based on availability
                    if (!existingSchedule.is_available) {
                        $(this).addClass('disabled booked').prop('disabled', true);
                    } else {
                        $(this).removeClass('btn--secondary').addClass('btn--primary');
                        if (existingSchedule.outdated) {
                            $(this).addClass('outdated-checked');
                        }
                    }
                }
            });

            updateHiddenInput();
        }

        // Check and update time slot availability
        function updateTimeSlotAvailability() {
            $('.time-slot-btn').each(function() {
                const timeSlotStartTime = $(this).text().split(' - ')[0];
                const [slotHour, slotMinute] = timeSlotStartTime.split(':').map(Number);
                const timeSlotId = $(this).data('id');

                // Create DateTime objects for comparison
                const selectedDateTime = new Date(selectedDate + 'T' + String(slotHour).padStart(2, '0') + ':' + String(slotMinute).padStart(2, '0') + ':00');
                const currentDateTime = new Date();

                // Check if time slot is outdated (selected datetime <= current datetime)
                if (selectedDateTime <= currentDateTime) {
                    // Don't disable if it's already in initialData (allow keeping existing outdated schedules)
                    const existingSchedule = initialData.find(item => item.time_slot_id === timeSlotId);
                    if (!existingSchedule) {
                        $(this).addClass('disabled').prop('disabled', true);
                        selectedTimeSlotIds = selectedTimeSlotIds.filter(id => id !== timeSlotId);
                        $(this).removeClass('btn--primary').addClass('btn--secondary');
                    }
                }
            });

            updateHiddenInput();
        }

        // Update hidden input value
        function updateHiddenInput() {
            $('#timeSlotIds').val(selectedTimeSlotIds.join(','));
        }

        // Validate form data
        function validateFormData() {
            const errors = [];

            if (!$('#scheduleDate').val()) {
                errors.push('Vui lòng chọn ngày làm việc');
            }

            if (selectedTimeSlotIds.length === 0) {
                errors.push('Vui lòng chọn ít nhất một ca làm việc');
            }

            return errors;
        }

        // Get schedule IDs for update/delete operations
        function getScheduleIdsForUpdate() {
            const parserTimeSlots = selectedTimeSlotIds.map(id => parseInt(id));

            // Get outdated schedules (these will be kept as-is or updated)
            const outdatedScheduleIds = initialData
                .filter(item => item.outdated === true)
                .map(item => item.staff_schedule_id);

            // Get schedules that are currently selected and exist in initialData
            const existingSelectedScheduleIds = initialData
                .filter(item => parserTimeSlots.includes(item.time_slot_id))
                .map(item => item.staff_schedule_id);

            // Get newly selected time slots (not in initialData)
            const newTimeSlotIds = parserTimeSlots.filter(timeSlotId =>
                !initialData.some(item => item.time_slot_id === timeSlotId)
            );

            // Get schedules to be removed (were in initialData but not selected now)
            const removedScheduleIds = initialData
                .filter(item => !parserTimeSlots.includes(item.time_slot_id) && !item.outdated)
                .map(item => item.staff_schedule_id);

            return {
                outdatedScheduleIds,
                existingSelectedScheduleIds,
                newTimeSlotIds,
                removedScheduleIds,
                allScheduleIds: [...new Set([...outdatedScheduleIds, ...existingSelectedScheduleIds])],
                selectedTimeSlotIds: parserTimeSlots
            };
        }

        // Initialize page
        initializeTimeSlotButtons();
        updateTimeSlotAvailability();

        // Handle time slot button clicks
        $('.time-slot-btn').click(function() {
            if ($(this).hasClass('disabled') || $(this).hasClass('booked')) {
                return; // Do nothing if disabled or booked
            }

            const timeSlotId = $(this).data('id');

            if ($(this).hasClass('btn--primary')) {
                // Deselect
                $(this).removeClass('btn--primary').addClass('btn--secondary');
                selectedTimeSlotIds = selectedTimeSlotIds.filter(id => id !== timeSlotId);
            } else {
                // Select
                $(this).removeClass('btn--secondary').addClass('btn--primary');
                selectedTimeSlotIds.push(timeSlotId);
            }

            updateHiddenInput();
        });

        // Handle form submission
        $('#form-add').submit(function(e) {
            e.preventDefault();

            // Validate form
            const validationErrors = validateFormData();
            if (validationErrors.length > 0) {
                swAlert('Lỗi', validationErrors.join('<br>'), 'error');
                return;
            }

            const scheduleData = getScheduleIdsForUpdate();

            const formData = new FormData();
            formData.append('date', $('#scheduleDate').val());
            formData.append('time_slot_ids', selectedTimeSlotIds.join(','));
            formData.append('schedule_ids', scheduleData.allScheduleIds.join(','));
            formData.append('removed_schedule_ids', scheduleData.removedScheduleIds.join(','));

            $.ajax({
                url: '<?= base_url('/admin/staff/update/schedule/' . $staff->user_id . '/' . $date) ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    swAlert('Thông báo', response.message, 'success');
                    if (response.status) {
                        setTimeout(function() {
                            window.location.href = '<?= base_url('/admin/staff/schedule/' . $staff->user_id) ?>';
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    swAlert('Thông báo', xhr.responseJSON?.message || 'Có lỗi xảy ra', 'error');
                }
            });
        });
    });
</script>
<?php end_section() ?>
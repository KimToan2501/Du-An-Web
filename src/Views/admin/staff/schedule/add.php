<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<section class="admin-form-wrapper">
    <form id="form-add" class="admin-form">
        <div class="admin-form__group">
            <label for="scheduleDate" class="admin-form__label">Ngày làm việc (*)</label>
            <input type="date" id="scheduleDate" name="date" class="admin-form__input" required>
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
            <button type="submit" class="btn btn--primary">Thêm lịch làm việc</button>
            <a href="<?= base_url('/admin/staff/schedule/' . $metadata->user_id) ?>" class="btn btn--gray ml-2">Hủy</a>
        </div>
    </form>
</section>

<?php start_section('scripts') ?>
<script>
    $(document).ready(function() {
        let selectedTimeSlotIds = [];
        const today = getToday();
        $('#scheduleDate').val(today).attr('min', today);

        function updateTimeSlotAvailability() {
            const today = new Date().toISOString().slice(0, 10);
            const selectedDate = $('#scheduleDate').val();
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();

            $('.time-slot-btn').each(function() {
                const timeSlotStartTime = $(this).text().split(' - ')[0];
                const [slotHour, slotMinute] = timeSlotStartTime.split(':').map(Number);

                if (selectedDate <= today) {
                    if (slotHour < currentHour || (slotHour === currentHour && slotMinute <= currentMinute)) {
                        $(this).addClass('disabled').prop('disabled', true);
                        // Remove from selected if it was selected and now disabled
                        const timeSlotId = $(this).data('id');
                        selectedTimeSlotIds = selectedTimeSlotIds.filter(id => id !== timeSlotId);
                        $(this).removeClass('btn--primary').addClass('btn--secondary');
                    } else {
                        $(this).removeClass('disabled').prop('disabled', false);
                    }
                } else {
                    $(this).removeClass('disabled').prop('disabled', false);
                }
            });
            $('#timeSlotIds').val(selectedTimeSlotIds.join(','));
        }

        // Initial check on page load
        updateTimeSlotAvailability();

        // Update when date changes
        $('#scheduleDate').change(function() {
            updateTimeSlotAvailability();
        });

        $('.time-slot-btn').click(function() {
            if ($(this).hasClass('disabled')) {
                return; // Do nothing if disabled
            }

            const timeSlotId = $(this).data('id');
            if ($(this).hasClass('btn--primary')) {
                $(this).removeClass('btn--primary').addClass('btn--secondary');
                selectedTimeSlotIds = selectedTimeSlotIds.filter(id => id !== timeSlotId);
            } else {
                $(this).removeClass('btn--secondary').addClass('btn--primary');
                selectedTimeSlotIds.push(timeSlotId);
            }
            $('#timeSlotIds').val(selectedTimeSlotIds.join(','));
        });

        $('#form-add').submit(function(e) {
            e.preventDefault();

            const scheduleDate = $('#scheduleDate').val();
            const timeSlotIds = $('#timeSlotIds').val();

            if (!scheduleDate) {
                swAlert('Lỗi', 'Vui lòng chọn ngày làm việc.', 'error');
                return;
            }

            if (selectedTimeSlotIds.length === 0) {
                swAlert('Lỗi', 'Vui lòng chọn ít nhất một ca làm việc.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('date', scheduleDate);
            formData.append('time_slot_ids', timeSlotIds);

            $.ajax({
                url: '<?= base_url('/admin/staff/add/schedule/' . $metadata->user_id) ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    swAlert('Thông báo', response.message, 'success');
                    if (response.status) {
                        setTimeout(function() {
                            window.location.href = '<?= base_url('/admin/staff/schedule/' . $metadata->user_id) ?>';
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
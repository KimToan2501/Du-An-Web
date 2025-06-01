<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<div class="admin-form-wrapper">
    <div class="content">
        <form id="addBookingForm" class="admin-form">
            <div class="admin-form__row">
                <div class="admin-form__group">
                    <label class="admin-form__label" for="user_id">Khách hàng <span class="required">*</span></label>
                    <select id="user_id" name="user_id" required class="admin-form__select">
                        <option value="">Chọn khách hàng</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer->user_id ?>"><?= htmlspecialchars($customer->name) ?> - <?= htmlspecialchars($customer->email) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="admin-form__group">
                    <label class="admin-form__label" for="staff_id">Nhân viên <span class="required">*</span></label>
                    <select id="staff_id" name="staff_id" required class="admin-form__select">
                        <option value="">Chọn nhân viên</option>
                        <?php foreach ($staff as $staffMember): ?>
                            <option value="<?= $staffMember->user_id ?>"><?= htmlspecialchars($staffMember->name) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <div class="admin-form__row">
                <div class="admin-form__group">
                    <label class="admin-form__label" for="booking_date">Ngày booking <span class="required">*</span></label>
                    <input type="date" id="booking_date" name="booking_date" required class="admin-form__input">
                </div>

                <div class="admin-form__group">
                    <label class="admin-form__label" for="time_slot_id">Giờ <span class="required">*</span></label>
                    <select id="time_slot_id" name="time_slot_id" required class="admin-form__select">
                        <option value="">Chọn giờ</option>
                        <?php foreach ($timeSlots as $timeSlot): ?>
                            <option value="<?= $timeSlot->time_slot_id ?>"><?= $timeSlot->start_time ?> - <?= $timeSlot->end_time ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <div class="admin-form__row">
                <div class="admin-form__group">
                    <label class="admin-form__label" for="status">Trạng thái</label>
                    <select id="status" name="status" class="admin-form__select">
                        <?php foreach ($statuses as $key => $status): ?>
                            <option value="<?= $key ?>" <?= $key === 'pending' ? 'selected' : '' ?>><?= $status ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="admin-form__group">
                    <label class="admin-form__label" for="payment_method">Phương thức thanh toán</label>
                    <select id="payment_method" name="payment_method" class="admin-form__select">
                        <?php foreach ($paymentMethods as $key => $method): ?>
                            <option value="<?= $key ?>" <?= $key === 'cash' ? 'selected' : '' ?>><?= $method ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <div class="admin-form__row">
                <div class="admin-form__group">
                    <label class="admin-form__label" for="payment_status">Trạng thái thanh toán</label>
                    <select id="payment_status" name="payment_status" class="admin-form__select">
                        <?php foreach ($paymentStatuses as $key => $status): ?>
                            <option value="<?= $key ?>" <?= $key === 'pending' ? 'selected' : '' ?>><?= $status ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="admin-form__group">
                    <label class="admin-form__label" for="total_pets">Số thú cưng</label>
                    <input type="number" id="total_pets" name="total_pets" min="1" value="1" class="admin-form__input">
                </div>
            </div>

            <div class="admin-form__row">
                <div class="admin-form__group">
                    <label class="admin-form__label" for="total_services">Số dịch vụ</label>
                    <input type="number" id="total_services" name="total_services" min="1" value="1" class="admin-form__input">
                </div>

                <div class="admin-form__group">
                    <label class="admin-form__label" for="total_duration">Thời gian (phút)</label>
                    <input type="number" id="total_duration" name="total_duration" min="0" value="0" class="admin-form__input">
                </div>
            </div>

            <div class="admin-form__row">
                <div class="admin-form__group">
                    <label class="admin-form__label" for="subtotal">Tạm tính (VND)</label>
                    <input type="number" id="subtotal" name="subtotal" min="0" value="0" class="admin-form__input">
                </div>

                <div class="admin-form__group">
                    <label class="admin-form__label" for="discount_code">Mã giảm giá</label>
                    <input type="text" id="discount_code" name="discount_code" class="admin-form__input" placeholder="Nhập mã giảm giá">
                </div>
            </div>

            <div class="admin-form__row">
                <div class="admin-form__group">
                    <label class="admin-form__label" for="discount_percent">Giảm giá (%)</label>
                    <input type="number" id="discount_percent" name="discount_percent" min="0" max="100" value="0" class="admin-form__input">
                </div>

                <div class="admin-form__group">
                    <label class="admin-form__label" for="discount_amount">Số tiền giảm (VND)</label>
                    <input type="number" id="discount_amount" name="discount_amount" min="0" value="0" class="admin-form__input">
                </div>
            </div>

            <div class="admin-form__group">
                <label class="admin-form__label" for="total_amount">Tổng tiền (VND)</label>
                <input type="number" id="total_amount" name="total_amount" min="0" value="0" class="admin-form__input">
            </div>

            <div class="admin-form__group">
                <label class="admin-form__label" for="notes">Ghi chú</label>
                <textarea id="notes" name="notes" class="admin-form__textarea" rows="3" placeholder="Ghi chú chung về booking"></textarea>
            </div>

            <div class="admin-form__group">
                <label class="admin-form__label" for="customer_notes">Ghi chú khách hàng</label>
                <textarea id="customer_notes" name="customer_notes" class="admin-form__textarea" rows="3" placeholder="Ghi chú từ khách hàng"></textarea>
            </div>

            <div class="admin-form__group">
                <label class="admin-form__label" for="staff_notes">Ghi chú nhân viên</label>
                <textarea id="staff_notes" name="staff_notes" class="admin-form__textarea" rows="3" placeholder="Ghi chú từ nhân viên"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary">Thêm booking</button>
                <a href="/admin/booking" class="btn btn--gray">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>

<?php start_section('scripts') ?>
<script>
    $(document).ready(function() {
        // Set min date to today
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const todayFormatted = `${yyyy}-${mm}-${dd}`;
        $('#booking_date').attr('min', todayFormatted);

        // Calculate total amount
        function calculateTotal() {
            const subtotal = parseFloat($('#subtotal').val()) || 0;
            const discountPercent = parseFloat($('#discount_percent').val()) || 0;
            const discountAmount = parseFloat($('#discount_amount').val()) || 0;

            let total = subtotal;
            
            // Apply percentage discount first
            if (discountPercent > 0) {
                total = total * (1 - discountPercent / 100);
            }
            
            // Apply amount discount
            total = total - discountAmount;
            
            // Ensure total is not negative
            total = Math.max(0, total);
            
            $('#total_amount').val(Math.round(total));
        }

        // Auto calculate discount amount when percent changes
        $('#discount_percent').on('input', function() {
            const subtotal = parseFloat($('#subtotal').val()) || 0;
            const percent = parseFloat($(this).val()) || 0;
            const discountAmount = subtotal * (percent / 100);
            $('#discount_amount').val(Math.round(discountAmount));
            calculateTotal();
        });

        // Recalculate when any relevant field changes
        $('#subtotal, #discount_amount').on('input', calculateTotal);

        $('#addBookingForm').on('submit', function(e) {
            e.preventDefault();

            const userId = $('#user_id').val();
            const staffId = $('#staff_id').val();
            const bookingDate = $('#booking_date').val();
            const timeSlotId = $('#time_slot_id').val();

            if (!userId || !staffId || !bookingDate || !timeSlotId) {
                swAlert('Thông báo', 'Vui lòng điền đầy đủ thông tin bắt buộc', 'error');
                return;
            }

            const formData = new FormData(this);
            
            $.ajax({
                url: '<?= base_url('/admin/booking/add') ?>',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    swAlert('Thông báo', response.message, 'success');
                    setTimeout(function() {
                        window.location.href = '/admin/booking';
                    }, 1500);
                },
                error: function(error) {
                    swAlert('Thông báo', error.responseJSON?.message || 'Đã xảy ra lỗi', 'error');
                }
            });
        });
    });
</script>
<?php end_section() ?>
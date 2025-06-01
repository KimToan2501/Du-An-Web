<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php start_section('links'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/cart.css') ?>">
<?php end_section(); ?>

<!-- Start: Main -->
<main class="flex-grow-1">
    <!-- Progress Header -->
    <div class="progress-container">
        <div class="container">
            <?php include_partial('client/stepper-cart') ?>
            <div class="row align-items-center mt-3">
                <div class="col-md-8">
                    <h2 class="mb-1"><i class="fas fa-clipboard-check me-2"></i>Xem Lại Thông Tin Đặt Lịch</h2>
                    <p class="mb-0">Bước 4/4 - Vui lòng kiểm tra lại thông tin trước khi hoàn tất</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="<?= base_url('/cart/staff') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="row">
            <!-- Left Column - Booking Details -->
            <div class="col-lg-8">
                <!-- Services Summary -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list-check me-2"></i>Dịch Vụ Đã Chọn
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($selected_services as $index => $service): ?>
                            <div class="d-flex align-items-center mb-3 <?= $index < count($selected_services) - 1 ? 'border-bottom pb-3' : '' ?>">
                                <img src="<?= show_pet_avatar($service['image']) ?>"
                                    alt="<?= htmlspecialchars($service['name']) ?>"
                                    class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold"><?= htmlspecialchars($service['name']) ?></h6>
                                    <p class="mb-1 text-muted small"><?= htmlspecialchars($service['description'] ?? 'Dịch vụ chăm sóc thú cưng chuyên nghiệp') ?></p>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-info me-2">SL: <?= $service['quantity'] ?></span>
                                        <span class="text-primary fw-bold"><?= format_price($service['price_new']) ?></span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">
                                        <?= format_price($service['price_new'] * $service['quantity']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Pet Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-paw me-2"></i>Thú Cưng Đã Chọn
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($pets as $pet): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <img src="<?= show_pet_avatar($pet->avatar_url) ?>"
                                            alt="<?= htmlspecialchars($pet->name) ?>"
                                            class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($pet->name) ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-venus-mars me-1"></i><?= getGenderName($pet->gender) ?>
                                                <span class="mx-2">|</span>
                                                <i class="fas fa-weight me-1"></i><?= htmlspecialchars($pet->weight) ?> kg
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Staff & Schedule Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-tie me-2"></i>Nhân Viên & Lịch Hẹn
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($staff_selected): ?>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="staff-avatar-lg">
                                            <img src="<?= show_avatar($staff_selected->avatar_url) ?>"
                                                alt="<?= htmlspecialchars($staff_selected->name) ?>"
                                                class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                        </div>

                                        <div>
                                            <h5 class="mb-1 fw-bold"><?= htmlspecialchars($staff_selected->name) ?></h5>
                                            <p class="mb-0 text-muted">
                                                <i class="fas fa-star text-warning me-1"></i>
                                                Nhân viên chuyên nghiệp
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-end text-md-start">
                                        <div class="mb-2">
                                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                                            <strong><?= format_day_vn($customer_info['selected_date']) ?></strong>
                                        </div>
                                        <?php foreach ($staff_info['selected_appointments'] as $appointment): ?>
                                            <div class="mb-1">
                                                <i class="fas fa-clock text-success me-2"></i>
                                                <span class="badge bg-success"><?= htmlspecialchars($appointment['timeRange']) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Thông Tin Khách Hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Tên khách hàng</label>
                                    <p class="mb-0"><?= htmlspecialchars($customer_info['user_info']['name'] ?? 'Không xác định') ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Số điện thoại</label>
                                    <p class="mb-0">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        <?= htmlspecialchars($customer_info['user_info']['phone'] ?? 'Không xác định') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Email</label>
                                    <p class="mb-0">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <?= htmlspecialchars($customer_info['user_info']['email'] ?? 'Không xác định') ?>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Địa chỉ</label>
                                    <p class="mb-0">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <?= htmlspecialchars($customer_info['user_info']['address'] ?? 'Không xác định') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="col-lg-4">
                <!-- Payment Summary -->
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>Tóm Tắt Đơn Hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Service Lines -->
                        <div class="mb-3">
                            <?php
                            $subtotal = 0;
                            foreach ($selected_services as $service):
                                $serviceTotal = $service['price_new'] * $service['quantity'];
                                $subtotal += $serviceTotal;
                            ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <span class="small"><?= htmlspecialchars($service['name']) ?></span>
                                        <span class="badge bg-secondary ms-1"><?= $service['quantity'] ?></span>
                                    </div>
                                    <span class="small"><?= format_price($serviceTotal) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>

                        <!-- Subtotal -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span>
                            <span><?= format_price($subtotal) ?></span>
                        </div>

                        <!-- Discount -->
                        <?php if (isset($booking_info['discount_percent']) && $booking_info['discount_percent'] > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>
                                    <i class="fas fa-tag me-1"></i>
                                    Giảm giá (<?= $booking_info['discount_percent'] ?>%)
                                </span>
                                <span>-<?= format_price($subtotal * $booking_info['discount_percent'] / 100) ?></span>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <!-- Total -->
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold fs-5">Tổng cộng</span>
                            <span class="fw-bold fs-5 text-primary">
                                <?= format_price($booking_info['total_price'] ?? $subtotal) ?>
                            </span>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-3 p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-credit-card text-primary me-2"></i>
                                <div>
                                    <span class="fw-bold">Phương thức thanh toán</span><br>
                                    <small class="text-muted">
                                        <?php
                                        $paymentMethod = $customer_info['payment_method'] ?? 'cash';
                                        switch ($paymentMethod) {
                                            case 'card':
                                                echo '<i class="fas fa-credit-card me-1"></i>Thẻ tín dụng';
                                                break;
                                            case 'vnpay':
                                                echo '<i class="fas fa-mobile-alt me-1"></i>VNPay';
                                                break;
                                            default:
                                                echo '<i class="fas fa-money-bill me-1"></i>Tiền mặt';
                                        }
                                        ?>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Confirm Button -->
                        <button type="button" class="btn btn-primary btn-lg w-100 mb-2" id="confirm-booking-btn">
                            <i class="fas fa-check-circle me-2"></i>
                            Xác Nhận Đặt Lịch
                        </button>

                        <!-- Terms -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="agree-terms" required>
                            <label class="form-check-label small text-muted" for="agree-terms">
                                Tôi đồng ý với <a href="#" class="text-primary">điều khoản dịch vụ</a> và <a href="#" class="text-primary">chính sách bảo mật</a>
                            </label>
                        </div>

                        <!-- Customer Support -->
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-headset me-1"></i>
                                Cần hỗ trợ? Gọi <a href="tel:1900123456" class="text-primary">1900 123 456</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- End: Main -->

<?php start_section('scripts') ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmBtn = document.getElementById('confirm-booking-btn');
        const agreeTerms = document.getElementById('agree-terms');

        // Enable/disable confirm button based on terms agreement
        agreeTerms.addEventListener('change', function() {
            confirmBtn.disabled = !this.checked;
            if (this.checked) {
                confirmBtn.classList.remove('btn-secondary');
                confirmBtn.classList.add('btn-primary');
            } else {
                confirmBtn.classList.remove('btn-primary');
                confirmBtn.classList.add('btn-secondary');
            }
        });

        // Initially disable button
        confirmBtn.disabled = true;
        confirmBtn.classList.remove('btn-primary');
        confirmBtn.classList.add('btn-secondary');

        // Handle confirm booking
        confirmBtn.addEventListener('click', function() {
            if (!agreeTerms.checked) {
                swAlert('Thông báo', 'Vui lòng đồng ý với điều khoản dịch vụ để tiếp tục!', 'warning');
                return;
            }

            // Show loading state
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
            this.disabled = true;

            // Call confirm booking API
            confirmBooking();
        });

        function confirmBooking() {
            loadAjaxStatus('start');

            $.ajax({
                url: '<?= base_url('/cart/confirm-booking') ?>',
                type: 'POST',
                data: null,
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    console.log('Response:', response);
                    if (!response.metadata) {
                        swAlert('Thông báo!', 'Đặt lịch không thành công!', 'info');
                    } else {
                        swAlert('Thành công!', response.message, 'success');

                        setTimeout(function() {
                            if (response.metadata.payment_url) {
                                window.location.href = response.metadata.payment_url;
                            } else {
                                window.location.href = '<?= base_url('/booking/response?booking_code=') ?>' + response.metadata.booking_code;
                            }
                        }, 2000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    swAlert('Lỗi!', xhr.responseJSON.message, 'error');
                },
                complete: function() {
                    resetConfirmButton();
                    loadAjaxStatus('stop');
                }
            });
        }

        function resetConfirmButton() {
            const confirmBtn = document.getElementById('confirm-booking-btn');
            confirmBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Xác Nhận Đặt Lịch';
            confirmBtn.disabled = !document.getElementById('agree-terms').checked;

            if (!confirmBtn.disabled) {
                confirmBtn.classList.remove('btn-secondary');
                confirmBtn.classList.add('btn-primary');
            }
        }
    });
</script>
<?php end_section() ?>
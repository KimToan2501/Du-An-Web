<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <!-- Kết quả thanh toán -->
      <div class="result-card card mb-4">
        <div class="card-body text-center py-5">
          <?php if ($is_vnpay_payment): ?>
            <?php if ($payment_success): ?>
              <i class="fas fa-check-circle success-icon"></i>
              <h3 class="text-success mb-3">Thanh toán thành công!</h3>
              <p class="text-muted mb-4"><?= htmlspecialchars($message) ?></p>
            <?php else: ?>
              <i class="fas fa-times-circle error-icon"></i>
              <h3 class="text-danger mb-3">Thanh toán thất bại!</h3>
              <p class="text-muted mb-4"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
          <?php else: ?>
            <i class="fas fa-info-circle text-primary" style="font-size: 4rem; margin-bottom: 1rem;"></i>
            <h3 class="text-primary mb-3">Thông tin đặt lịch</h3>
            <p class="text-muted mb-4"><?= htmlspecialchars($message) ?></p>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($booking): ?>
        <!-- Thông tin booking -->
        <div class="result-card card mb-4">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
              <i class="fas fa-calendar-alt me-2"></i>
              Thông tin đặt lịch
            </h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="info-row">
                  <strong>Mã đặt lịch:</strong>
                  <span class="text-primary fw-bold"><?= $booking['booking_code'] ?></span>
                </div>
                <div class="info-row">
                  <strong>Ngày đặt:</strong>
                  <?= date('d/m/Y', strtotime($booking['booking_date'])) ?>
                </div>
                <div class="info-row">
                  <strong>Trạng thái:</strong>
                  <?php
                  $statusClasses = [
                    'pending' => 'bg-warning text-dark',
                    'confirmed' => 'bg-success text-white',
                    'in_progress' => 'bg-info',
                    'completed' => 'bg-primary',
                    'cancelled' => 'bg-danger',
                    'no_show' => 'bg-secondary'
                  ];
                  $statusNames = [
                    'pending' => 'Chờ xác nhận',
                    'confirmed' => 'Đã xác nhận',
                    'in_progress' => 'Đang thực hiện',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Đã hủy',
                    'no_show' => 'Không đến'
                  ];
                  $statusClass = $statusClasses[$booking['status']] ?? 'bg-secondary';
                  $statusName = $statusNames[$booking['status']] ?? $booking['status'];
                  ?>
                  <span class="status-badge <?= $statusClass ?>"><?= $statusName ?></span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-row">
                  <strong>Tổng số thú cưng:</strong>
                  <?= $booking['total_pets'] ?>
                </div>
                <div class="info-row">
                  <strong>Tổng số dịch vụ:</strong>
                  <?= $booking['total_services'] ?>
                </div>
                <div class="info-row">
                  <strong>Thời gian dự kiến:</strong>
                  <?= $booking['total_duration'] ?> phút
                </div>
                <div class="info-row">
                  <strong>Tổng tiền:</strong>
                  <span class="text-danger fw-bold"><?= format_price($booking['total_amount']) ?></span>
                </div>
              </div>
            </div>

            <?php if ($booking['notes']): ?>
              <div class="mt-3">
                <strong>Ghi chú:</strong>
                <p class="mt-1 text-muted"><?= nl2br(htmlspecialchars($booking['notes'])) ?></p>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Thông tin khách hàng -->
        <div class="result-card card mb-4">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0">
              <i class="fas fa-user me-2"></i>
              Thông tin khách hàng
            </h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="info-row">
                  <strong>Tên khách hàng:</strong>
                  <?= htmlspecialchars($booking['customer_name'] ?? 'N/A') ?>
                </div>
                <div class="info-row">
                  <strong>Email:</strong>
                  <?= htmlspecialchars($booking['customer_email'] ?? 'N/A') ?>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-row">
                  <strong>Số điện thoại:</strong>
                  <?= htmlspecialchars($booking['customer_phone'] ?? 'N/A') ?>
                </div>
                <div class="info-row">
                  <strong>Địa chỉ:</strong>
                  <?= htmlspecialchars($booking['customer_address'] ?? 'N/A') ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Thông tin nhân viên -->
        <?php if ($booking['staff_name']): ?>
          <div class="result-card card mb-4">
            <div class="card-header bg-success text-white">
              <h5 class="mb-0">
                <i class="fas fa-user-tie me-2"></i>
                Nhân viên phụ trách
              </h5>
            </div>
            <div class="card-body">
              <div class="info-row">
                <strong>Tên nhân viên:</strong>
                <?= htmlspecialchars($booking['staff_name']) ?>
              </div>
              <div class="info-row">
                <strong>Email:</strong>
                <?= htmlspecialchars($booking['staff_email'] ?? 'N/A') ?>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Thông tin dịch vụ -->
        <?php
        $services = isset($booking['id']) ? App\Models\Booking::getBookingServices($booking['id']) : [];
        // dd($services);
        if ($services):
        ?>
          <div class="result-card card mb-4">
            <div class="card-header bg-warning text-dark">
              <h5 class="mb-0">
                <i class="fas fa-cut me-2"></i>
                Dịch vụ đã đặt
              </h5>
            </div>
            <div class="card-body">
              <?php foreach ($services as $service): ?>
                <div class="service-item">
                  <div class="row">
                    <div class="col-md-6">
                      <strong><?= htmlspecialchars($service['service_name']) ?></strong>
                      <br>
                      <small class="text-muted"><?= htmlspecialchars($service['service_type_name'] ?? '') ?></small>
                    </div>

                    <div class="col-md-3 text-center">
                      <strong>Số lượng:</strong> <?= $service['quantity'] ?>
                      <br>
                      <strong>Đơn giá:</strong> <?= format_price($service['unit_price']) ?>
                    </div>

                    <div class="col-md-3 text-end">
                      <strong>Thành tiền:</strong>
                      <span class="text-danger"><?= format_price($service['total_price']) ?></span>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Thông tin thú cưng -->
        <?php
        $pets = isset($booking['id']) ? App\Models\Booking::getBookingPets($booking['id']) : [];
        if ($pets):
        ?>
          <div class="result-card card mb-4">
            <div class="card-header bg-secondary text-white">
              <h5 class="mb-0">
                <i class="fas fa-paw me-2"></i>
                Thú cưng
              </h5>
            </div>
            <div class="card-body">
              <?php foreach ($pets as $pet): ?>
                <div class="pet-item">
                  <div class="row align-items-center">
                    <div class="col-md-2">
                      <?php if ($pet['avatar_url']): ?>
                        <img src="<?= htmlspecialchars($pet['avatar_url']) ?>"
                          class="img-fluid rounded-circle"
                          alt="<?= htmlspecialchars($pet['pet_name']) ?>"
                          style="width: 50px; height: 50px; object-fit: cover;">
                      <?php else: ?>
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                          style="width: 50px; height: 50px;">
                          <i class="fas fa-paw text-muted"></i>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="col-md-10">
                      <div class="row">
                        <div class="col-md-6">
                          <strong><?= htmlspecialchars($pet['pet_name']) ?></strong>
                          <br>
                          <small class="text-muted">
                            <?= htmlspecialchars($pet['type']) ?> - <?= htmlspecialchars($pet['breed']) ?>
                          </small>
                        </div>
                        <div class="col-md-6">
                          <small class="text-muted">
                            Tuổi: <?= $pet['age'] ?> <?= $pet['age_unit'] ?> |
                            Giới tính: <?= $pet['gender'] ?> |
                            Cân nặng: <?= $pet['weight'] ?>kg
                          </small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Thông tin thanh toán -->
        <div class="result-card card mb-4">
          <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
              <i class="fas fa-credit-card me-2"></i>
              Thông tin thanh toán
            </h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="info-row">
                  <strong>Phương thức:</strong>
                  <?php
                  $paymentMethods = [
                    'cash' => 'Tiền mặt',
                    'vnpay' => 'VNPay',
                    'momo' => 'MoMo',
                    'bank_transfer' => 'Chuyển khoản'
                  ];
                  echo $paymentMethods[$booking['payment_method']] ?? $booking['payment_method'];
                  ?>
                </div>
                <div class="info-row">
                  <strong>Trạng thái thanh toán:</strong>
                  <?php
                  $paymentStatusClasses = [
                    'pending' => 'bg-warning text-dark',
                    'paid' => 'bg-success',
                    'failed' => 'bg-danger'
                  ];
                  $paymentStatusNames = [
                    'pending' => 'Chờ thanh toán',
                    'paid' => 'Đã thanh toán',
                    'failed' => 'Thanh toán thất bại'
                  ];
                  $paymentStatusClass = $paymentStatusClasses[$booking['payment_status']] ?? 'bg-secondary';
                  $paymentStatusName = $paymentStatusNames[$booking['payment_status']] ?? $booking['payment_status'];
                  ?>
                  <span class="status-badge <?= $paymentStatusClass ?>"><?= $paymentStatusName ?></span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-row">
                  <strong>Tạm tính:</strong>
                  <?= number_format($booking['subtotal']) ?>đ
                </div>
                <?php if ($booking['discount_amount'] > 0): ?>
                  <div class="info-row">
                    <strong>Giảm giá:</strong>
                    -<?= number_format($booking['discount_amount']) ?>đ
                    <?php if ($booking['discount_percent'] > 0): ?>
                      (<?= $booking['discount_percent'] ?>%)
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
                <div class="info-row">
                  <strong>Tổng cộng:</strong>
                  <span class="text-danger fw-bold fs-5"><?= number_format($booking['total_amount']) ?>đ</span>
                </div>
                <?php if ($booking['paid_at']): ?>
                  <div class="info-row">
                    <strong>Thời gian thanh toán:</strong>
                    <?= date('d/m/Y H:i:s', strtotime($booking['paid_at'])) ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <?php if ($booking['discount_code']): ?>
              <div class="mt-3">
                <strong>Mã giảm giá:</strong>
                <span class="badge bg-success"><?= htmlspecialchars($booking['discount_code']) ?></span>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Thông tin VNPay (nếu có) -->
        <?php if ($is_vnpay_payment && isset($vnpay_result['transaction'])): ?>
          <div class="result-card card mb-4">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">
                <i class="fas fa-receipt me-2"></i>
                Chi tiết giao dịch VNPay
              </h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="info-row">
                    <strong>Mã giao dịch:</strong>
                    <?= htmlspecialchars($vnpay_result['transaction']->vnp_txn_ref ?? 'N/A') ?>
                  </div>
                  <div class="info-row">
                    <strong>Số giao dịch VNPay:</strong>
                    <?= htmlspecialchars($vnpay_result['transaction']->vnp_transaction_no ?? 'N/A') ?>
                  </div>
                  <div class="info-row">
                    <strong>Ngân hàng:</strong>
                    <?= htmlspecialchars($vnpay_result['transaction']->vnp_bank_code ?? 'N/A') ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="info-row">
                    <strong>Loại thẻ:</strong>
                    <?= htmlspecialchars($vnpay_result['transaction']->vnp_card_type ?? 'N/A') ?>
                  </div>
                  <div class="info-row">
                    <strong>Số tiền:</strong>
                    <?= number_format(($vnpay_result['transaction']->vnp_amount ?? 0) / 100) ?>đ
                  </div>
                  <?php if ($vnpay_result['transaction']->vnp_pay_date): ?>
                    <div class="info-row">
                      <strong>Thời gian thanh toán:</strong>
                      <?= date('d/m/Y H:i:s', strtotime($vnpay_result['transaction']->vnp_pay_date)) ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="text-center">
          <a href="/" class="btn btn-primary btn-lg me-3">
            <i class="fas fa-home me-2"></i>
            Về trang chủ
          </a>
          <a href="/service" class="btn btn-outline-primary btn-lg me-3">
            <i class="fas fa-plus me-2"></i>
            Đặt lịch mới
          </a>
          <a href="/user/booking" class="btn btn-outline-primary btn-lg me-3">
            <i class="fas fa-plus me-2"></i>
            Xem lịch hẹn
          </a>
        </div>

      <?php else: ?>
        <!-- Không tìm thấy booking -->
        <div class="result-card card">
          <div class="card-body text-center py-5">
            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem; margin-bottom: 1rem;"></i>
            <h4 class="text-warning mb-3">Không tìm thấy thông tin</h4>
            <p class="text-muted mb-4">
              Không thể tìm thấy thông tin đặt lịch. Vui lòng kiểm tra lại mã đặt lịch hoặc liên hệ với chúng tôi.
            </p>
            <a href="/" class="btn btn-primary">
              <i class="fas fa-home me-2"></i>
              Về trang chủ
            </a>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php start_section('scripts') ?>
<script>
  // Auto refresh nếu là payment pending
  <?php if ($booking && $booking['payment_status'] === 'pending' && $is_vnpay_payment): ?>
    setTimeout(function() {
      location.reload();
    }, 5000); // Refresh sau 5 giây
  <?php endif; ?>
</script>
<?php end_section() ?>

<?php start_section('links') ?>
<style>
  .result-card {
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border: none;
  }

  .status-badge {
    font-size: 0.9rem;
    padding: 8px 15px;
    border-radius: 20px;
  }

  .success-icon {
    color: #28a745;
    font-size: 4rem;
    margin-bottom: 1rem;
  }

  .error-icon {
    color: #dc3545;
    font-size: 4rem;
    margin-bottom: 1rem;
  }

  .info-row {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
  }

  .info-row:last-child {
    border-bottom: none;
  }

  .service-item {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 5px;
  }

  .pet-item {
    background: #e3f2fd;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 5px;
  }
</style>

<style media="print">
  body {
    background: white !important;
  }

  .btn,
  .bg-dark,
  .container-fluid.bg-dark {
    display: none !important;
  }

  .result-card {
    box-shadow: none !important;
    border: 1px solid #ddd !important;
  }
</style>

<?php end_section() ?>
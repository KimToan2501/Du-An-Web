<?php

use App\Models\Pet;

start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Start: Khối chức năng -->
<div class="controls-header">
  <a href="<?= base_url('/admin/booking') ?>" class="btn btn--primary">Quay lại</a>

  <div class="pet-detail-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="text-purple mb-0">
        <i class="fas fa-info-circle me-2"></i>
        Chi tiết đặt lịch: <?= htmlspecialchars($booking['booking_code']) ?>
      </h4>
    </div>

    <div class="detail-section">
      <h5><i class="fas fa-info-circle"></i>Thông tin cơ bản</h5>
      <div class="detail-grid">
        <div class="detail-item">
          <label>Mã đặt lịch</label>
          <span><?= htmlspecialchars($booking['booking_code']) ?></span>
        </div>
        <div class="detail-item">
          <label>Ngày đặt</label>
          <span><?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></span>
        </div>
        <div class="detail-item">
          <label>Trạng thái</label>
          <?php
          $statusClass = '';
          switch ($booking['status']) {
            case 'pending':
              $statusClass = 'badge--warning';
              break;
            case 'confirmed':
              $statusClass = 'badge--info';
              break;
            case 'completed':
              $statusClass = 'badge--success';
              break;
            case 'cancelled':
              $statusClass = 'badge--danger';
              break;
            case 'in_progress':
              $statusClass = 'badge--progress';
              break;
            default:
              $statusClass = 'badge--secondary';
          }
          ?>
          <span class="badge <?= $statusClass ?>"><?= $statuses[$booking['status']] ?? $booking['status'] ?></span>
        </div>
        <div class="detail-item">
          <label>Phương thức thanh toán</label>
          <span><?= getPaymentMethodName($booking['payment_method']) ?></span>
        </div>
        <div class="detail-item">
          <?php
          $paymentClass = '';
          switch ($booking['payment_status']) {
            case 'pending':
              $paymentClass = 'badge--warning';
              break;
            case 'paid':
              $paymentClass = 'badge--success';
              break;
            case 'failed':
              $paymentClass = 'badge--danger';
              break;
            default:
              $paymentClass = 'badge--secondary';
          }
          ?>
          <label>Trạng thái thanh toán</label>
          <span class="badge <?= $paymentClass ?>"><?= $paymentStatuses[$booking['payment_status']] ?? $booking['payment_status'] ?></span>
        </div>
        <?php if ($booking['paid_at']): ?>
          <div class="detail-item">
            <label>Thời gian thanh toán</label>
            <span><?= date('d/m/Y H:i', strtotime($booking['paid_at'])) ?></span>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="detail-section">
      <h5><i class="fas fa-list"></i>Thông tin dịch vụ</h5>
      <div class="detail-grid">
        <div class="detail-item">
          <label>Số lượng thú cưng</label>
          <span><?= $booking['total_pets']  ?> con</span>
        </div>
        <div class="detail-item">
          <label>Số lượng dịch vụ</label>
          <span><?= $booking['total_services']  ?> dịch vụ</span>
        </div>
        <div class="detail-item">
          <label>Thời gian dự kiến</label>
          <span><?= $booking['total_duration']  ?> phút</span>
        </div>
      </div>
    </div>

    <div class="detail-section">
      <h5><i class="fas fa-money-bill"></i>Thông tin thanh toán</h5>
      <div class="payment-summary">
        <div class="payment-row">
          <span>Tạm tính:</span>
          <span><?= number_format($booking['subtotal']) ?>đ</span>
        </div>
        <?php if ($booking['discount_amount']  > 0): ?>
          <div class="payment-row discount">
            <span>Giảm giá <?= $booking['discount_percent']  ?>%:</span>
            <span>-<?= number_format($booking['discount_amount']) ?>đ</span>
          </div>
        <?php endif; ?>
        <div class="payment-row total">
          <span class="fw-bold">Tổng cộng:</span>
          <span class="fw-bold text-purple"><?= number_format($booking['total_amount']) ?>đ</span>
        </div>
      </div>
    </div>

    <?php
    $petInstance = new Pet();
    // 1. Thông tin thú cưng (từ BookingPet)
    if (!empty($pets)): ?>
      <div class="detail-section">
        <h5><i class="fas fa-paw"></i>Thú cưng được đặt lịch</h5>
        <div class="pets-list">
          <?php foreach ($pets as $pet): ?>
            <div class="pet-item">
              <div class="pet-info">
                <div class="pet-avatar">
                  <img src="<?= show_pet_avatar($pet['avatar_url']) ?>" alt="<?= htmlspecialchars($pet['pet_name']) ?>">
                </div>
                <div class="pet-details">
                  <div class="pet-name fw-bold"><?= htmlspecialchars($pet['pet_name']) ?></div>
                  <div class="pet-breed text-muted"><?= htmlspecialchars($pet['breed']) ?> - <?= $petInstance->getTypeName($pet['type']) ?></div>
                  <div class="pet-age text-muted"><?= $pet['age'] ?> (<?= $petInstance->getAgeUnitName($pet['age_unit']) ?>) tuổi - <?= $petInstance->getGenderName($pet['gender']) ?></div>
                  <?php if (!empty($pet['special_notes'])): ?>
                    <div class="pet-special-notes">
                      <small class="text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <?= htmlspecialchars($pet['special_notes']) ?>
                      </small>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($services)): ?>
      <div class="detail-section">
        <h5><i class="fas fa-scissors"></i>Chi tiết dịch vụ</h5>
        <div class="pets-table">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Dịch vụ</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Giảm giá</th>
                <th>Thời gian</th>
                <th>Thành tiền</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($services as $service): ?>
                <tr class="pet-row">
                  <td>
                    <div class="service-info">
                      <div class="service-name fw-bold"><?= htmlspecialchars($service['service_name']) ?></div>
                      <?php if (!empty($service['service_description'])): ?>
                        <small class="text-muted"><?= htmlspecialchars($service['service_description']) ?></small>
                      <?php endif; ?>
                    </div>
                  </td>

                  <td><?= $service['quantity'] ?></td>

                  <td><?= number_format($service['unit_price']) ?>đ</td>

                  <td>
                    <?php if ($service['discount_percent'] > 0): ?>
                      <span class="text-success"><?= $service['discount_percent'] ?>%</span>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>

                  <td><?= $service['duration'] ?> phút</td>

                  <td class="fw-bold text-purple"><?= number_format($service['total_price']) ?>đ</td>
                </tr>
                <?php if (!empty($service['notes'])): ?>
                  <tr>
                    <td colspan="6">
                      <small class="text-muted">
                        <i class="fas fa-sticky-note me-1"></i>
                        Ghi chú: <?= htmlspecialchars($service['notes']) ?>
                      </small>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($staffSchedules)): ?>
      <div class="detail-section">
        <h5><i class="fas fa-user-tie"></i>Nhân viên phụ trách</h5>
        <div class="staff-list">
          <?php foreach ($staffSchedules as $item): ?>
            <div class="staff-item">
              <div class="staff-info d-flex align-items-center">
                <div class="staff-avatar me-3">
                  <img src="<?= $item['staff']['avatar'] ?>" alt="<?= htmlspecialchars($item['staff']['name']) ?>">
                </div>
                <div class="staff-details">
                  <div class="staff-name fw-bold"><?= htmlspecialchars($item['staff']['name']) ?></div>
                  <div class="staff-role text-muted"><?= htmlspecialchars($item['staff']['email']) ?></div>
                  <div class="staff-schedule text-muted">
                    <i class="fas fa-clock me-1"></i>
                    <?= date('d/m/Y', strtotime($booking['booking_date'])) ?> -
                    <?= $item['time_slot']['start_time'] ?> đến <?= $item['time_slot']['end_time'] ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($booking['customer_notes'])): ?>
      <div class="detail-section">
        <h5><i class="fas fa-comment"></i>Ghi chú khách hàng</h5>
        <div class="detail-item">
          <span><?= htmlspecialchars($booking['customer_notes']) ?></span>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($booking['staff_notes'])): ?>
      <div class="detail-section">
        <h5><i class="fas fa-user-tie"></i>Ghi chú nhân viên</h5>
        <div class="detail-item">
          <span><?= htmlspecialchars($booking['staff_notes']) ?></span>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($booking['status']  === 'cancelled' && !empty($booking['cancellation_reason'])): ?>
      <div class="detail-section">
        <h5><i class="fas fa-ban"></i>Lý do hủy</h5>
        <div class="detail-item">
          <span><?= htmlspecialchars($booking['cancellation_reason']) ?></span>
        </div>
      </div>
    <?php endif; ?>


    <?php
    // 5. Thông tin thanh toán VNPay (nếu có)
    if ($booking['payment_method'] === 'vnpay' && !empty($transaction)): ?>
      <div class="detail-section">
        <h5><i class="fas fa-credit-card"></i>Thông tin giao dịch VNPay</h5>
        <div class="payment-transaction">
          <div class="detail-grid">
            <div class="detail-item">
              <label>Mã giao dịch VNPay</label>
              <span><?= htmlspecialchars($transaction->vnp_transaction_no ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
              <label>Mã tham chiếu</label>
              <span><?= htmlspecialchars($transaction->vnp_txn_ref ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
              <label>Ngân hàng</label>
              <span><?= htmlspecialchars($transaction->vnp_bank_code ?? 'N/A') ?></span>
            </div>
            <div class="detail-item">
              <label>Loại thẻ</label>
              <span><?= htmlspecialchars($transaction->vnp_card_type ?? 'N/A') ?></span>
            </div>
            <?php if (!empty($transaction->vnp_pay_date)): ?>
              <div class="detail-item">
                <label>Thời gian thanh toán</label>
                <span><?= date('d/m/Y H:i:s', strtotime($transaction->vnp_pay_date)) ?></span>
              </div>
            <?php endif; ?>
            <div class="detail-item">
              <label>Trạng thái giao dịch</label>
              <span class="badge <?= $transaction->status === 'success' ? 'badge--success' : 'badge-danger' ?>">
                <?= $transaction->status === 'success' ? 'Thành công' : 'Thất bại' ?>
              </span>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<!-- End: Khối chức năng -->


<?php start_section('scripts') ?>
<script>
</script>
<?php end_section() ?>


<?php start_section('links'); ?>
<!-- Bootstrap 5 CDN -->

<link rel="stylesheet" href="<?= base_url('assets/css/account.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/booking-account.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/account-pet.css') ?>">

<style>
  /* Bootstrap Clone Utilities */
  .d-flex {
    display: flex !important;
  }

  .justify-content-between {
    justify-content: space-between !important;
  }

  .align-items-center {
    align-items: center !important;
  }

  .me-1 {
    margin-right: 0.25rem !important;
  }

  .me-2 {
    margin-right: 0.5rem !important;
  }

  .me-3 {
    margin-right: 1rem !important;
  }

  .mb-0 {
    margin-bottom: 0 !important;
  }

  .mb-4 {
    margin-bottom: 1.5rem !important;
  }

  .fw-bold {
    font-weight: 700 !important;
  }

  .text-muted {
    color: #6c757d !important;
  }

  /* Table Styles */
  .table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
    border-collapse: collapse;
  }

  .detail-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 8px;
  }

  .detail-section h5 {
    font-size: 1.25rem;
    color: #6f42c1;
    margin-bottom: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }


  .detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
  }

  .detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }

  .detail-item label {
    font-weight: 600;
    color: #495057;
    font-size: 0.875rem;
  }

  .detail-item span {
    color: #212529;
  }


  .pet-detail-content {
    margin: 2rem 0;
  }

  /* Badge Status Styles */
  .badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 20px;
    border: 2px solid transparent;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
  }

  .badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
  }

  .badge:hover::before {
    left: 100%;
  }

  .badge--progress {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #92400e;
    border-color: #fbbf24;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
    position: relative;
  }

  /* Booking Status Colors */
  .badge--warning {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #92400e;
    border-color: #fcd34d;
    box-shadow: 0 2px 8px rgba(251, 191, 36, 0.3);
  }

  .badge--info {
    background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
    color: #1e40af;
    border-color: #93c5fd;
    box-shadow: 0 2px 8px rgba(96, 165, 250, 0.3);
  }

  .badge--success {
    background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
    color: #065f46;
    border-color: #6ee7b7;
    box-shadow: 0 2px 8px rgba(52, 211, 153, 0.3);
  }

  .badge--danger {
    background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
    color: #991b1b;
    border-color: #fca5a5;
    box-shadow: 0 2px 8px rgba(248, 113, 113, 0.3);
  }

  .badge--secondary {
    background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
    color: #374151;
    border-color: #d1d5db;
    box-shadow: 0 2px 8px rgba(156, 163, 175, 0.3);
  }

  /* Hover Effects */
  .badge--warning:hover {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.4);
  }

  .badge--info:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(96, 165, 250, 0.4);
  }

  .badge--success:hover {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(52, 211, 153, 0.4);
  }

  .badge--danger:hover {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(248, 113, 113, 0.4);
  }

  .badge--secondary:hover {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(156, 163, 175, 0.4);
  }
</style>
<?php end_section(); ?>
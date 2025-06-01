<?php

use App\Core\Auth;
use App\Models\Booking;
use App\Models\Pet;
use App\Models\Review;

$auth = Auth::getInstance();
$user = $auth->user();

start_section('title'); ?>
<?= $title ?>
<?php end_section(); ?>

<?php start_section('links'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/account.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/booking-account.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/account-pet.css') ?>">
<?php end_section(); ?>

<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <?php include_partial('client/sidebar-profile') ?>

        <!-- Main Content -->
        <div class="col-md-9 p-4">
            <!-- Tab Navigation -->
            <?php include_partial('client/tab-profile') ?>

            <!-- Bookings Tab Content -->
            <div id="booking-tab" class="tab-content">
                <!-- Bookings Table -->
                <div class="pets-table-card">
                    <div class="pet s-header p-4 d-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-purple">Lịch Sử Đặt Lịch</h3>
                    </div>

                    <!-- Filter Options -->
                    <div class="filter-section p-4 mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-select" id="statusFilter">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="pending">Chờ xác nhận</option>
                                    <option value="confirmed">Đã xác nhận</option>
                                    <option value="in_progress">Đang thực hiện</option>
                                    <option value="completed">Hoàn thành</option>
                                    <option value="cancelled">Đã hủy</option>
                                    <option value="no_show">Không đến</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select" id="paymentStatusFilter">
                                    <option value="">Tất cả thanh toán</option>
                                    <option value="pending">Chờ thanh toán</option>
                                    <option value="paid">Đã thanh toán</option>
                                    <option value="failed">Thanh toán thất bại</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="dateFilter" placeholder="Chọn ngày">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-purple w-100" id="applyFilter">
                                    <i class="fas fa-filter"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table pets-table">
                            <thead>
                                <tr>
                                    <th>Mã đặt lịch</th>
                                    <th>Ngày đặt</th>
                                    <th>Trạng thái</th>
                                    <th>Thanh toán</th>
                                    <th>Tổng tiền</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($metadata)): ?>
                                    <?php foreach ($metadata as $booking): ?>
                                        <tr class="booking-row pet-row" data-booking-id="<?= $booking->id ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="pet-icon me-3">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </div>
                                                    <span class="pet-code fw-bold">
                                                        <?= htmlspecialchars($booking->booking_code) ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="booking-date">
                                                    <div class="fw-bold">
                                                        <?= date('d/m/Y', strtotime($booking->created_at)) ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?= date('H:i', strtotime($booking->created_at)) ?>
                                                    </small>
                                                </div>
                                            </td>

                                            <td>
                                                <span class="status-badge status-<?= $booking->status  ?>">
                                                    <?= getBookingStatusName($booking->status) ?>
                                                </span>
                                            </td>

                                            <td>
                                                <span class="payment-badge payment-<?= $booking->payment_status  ?>">
                                                    <?= getPaymentStatusName($booking->payment_status) ?>
                                                </span>
                                            </td>

                                            <td class="booking-amount">
                                                <span class="fw-bold text-purple">
                                                    <?= number_format($booking->total_amount) ?>đ
                                                </span>
                                            </td>

                                            <td>
                                                <button class="btn pet-detail-btn booking-detail-btn" data-booking-id="<?= $booking->id  ?>">
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <tr class="pet-detail-row" id="detail-<?= $booking->id  ?>">
                                            <td colspan="6">
                                                <div class="pet-detail-content">
                                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                                        <h4 class="text-purple mb-0">
                                                            <i class="fas fa-info-circle me-2"></i>
                                                            Chi tiết đặt lịch: <?= htmlspecialchars($booking->booking_code) ?>
                                                        </h4>
                                                        <div>
                                                            <?php if ($booking->status  === 'pending'): ?>
                                                                <button class="btn cancel-btn me-2" data-booking-id="<?= $booking->id  ?>">
                                                                    <i class="fas fa-times me-1"></i>Hủy lịch
                                                                </button>
                                                            <?php endif; ?>

                                                            <?php if ($booking->payment_status  === 'pending' && in_array($booking->status, ['pending', 'confirmed'])): ?>
                                                                <button class="btn payment-btn me-2" data-booking-id="<?= $booking->id  ?>">
                                                                    <i class="fas fa-credit-card me-1"></i>Thanh toán
                                                                </button>
                                                            <?php endif; ?>



                                                            <?php $reviews = Review::findByBookingId($booking->id); ?>

                                                            <?php if (!empty($reviews)): ?>
                                                                <button class="btn btn-success me-2" disabled>
                                                                    <i class="fas fa-check me-1"></i>Đã đánh giá
                                                                </button>
                                                            <?php else: ?>
                                                                <?php if ($booking->status === 'completed'): ?>
                                                                    <button class="btn review-btn me-2" data-booking-id="<?= $booking->id ?>">
                                                                        <i class="fas fa-star me-1"></i>Đánh giá dịch vụ
                                                                    </button>
                                                                <?php endif; ?>
                                                            <?php endif; ?>

                                                            <button class="btn close-btn" onclick="closeBookingDetail(<?= $booking->id  ?>)">
                                                                <i class="fas fa-times me-1"></i>Đóng
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="detail-section">
                                                        <h5><i class="fas fa-info-circle"></i>Thông tin cơ bản</h5>
                                                        <div class="detail-grid">
                                                            <div class="detail-item">
                                                                <label>Mã đặt lịch</label>
                                                                <span><?= htmlspecialchars($booking->booking_code) ?></span>
                                                            </div>
                                                            <div class="detail-item">
                                                                <label>Ngày đặt</label>
                                                                <span><?= date('d/m/Y H:i', strtotime($booking->created_at)) ?></span>
                                                            </div>
                                                            <div class="detail-item">
                                                                <label>Trạng thái</label>
                                                                <span class="status-badge status-<?= $booking->status  ?>">
                                                                    <?= getBookingStatusName($booking->status) ?>
                                                                </span>
                                                            </div>
                                                            <div class="detail-item">
                                                                <label>Phương thức thanh toán</label>
                                                                <span><?= getPaymentMethodName($booking->payment_method) ?></span>
                                                            </div>
                                                            <div class="detail-item">
                                                                <label>Trạng thái thanh toán</label>
                                                                <span class="payment-badge payment-<?= $booking->payment_status  ?>">
                                                                    <?= getPaymentStatusName($booking->payment_status) ?>
                                                                </span>
                                                            </div>
                                                            <?php if ($booking->paid_at): ?>
                                                                <div class="detail-item">
                                                                    <label>Thời gian thanh toán</label>
                                                                    <span><?= date('d/m/Y H:i', strtotime($booking->paid_at)) ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <div class="detail-section">
                                                        <h5><i class="fas fa-list"></i>Thông tin dịch vụ</h5>
                                                        <div class="detail-grid">
                                                            <div class="detail-item">
                                                                <label>Số lượng thú cưng</label>
                                                                <span><?= $booking->total_pets  ?> con</span>
                                                            </div>
                                                            <div class="detail-item">
                                                                <label>Số lượng dịch vụ</label>
                                                                <span><?= $booking->total_services  ?> dịch vụ</span>
                                                            </div>
                                                            <div class="detail-item">
                                                                <label>Thời gian dự kiến</label>
                                                                <span><?= $booking->total_duration  ?> phút</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="detail-section">
                                                        <h5><i class="fas fa-money-bill"></i>Thông tin thanh toán</h5>
                                                        <div class="payment-summary">
                                                            <div class="payment-row">
                                                                <span>Tạm tính:</span>
                                                                <span><?= number_format($booking->subtotal) ?>đ</span>
                                                            </div>
                                                            <?php if ($booking->discount_amount  > 0): ?>
                                                                <div class="payment-row discount">
                                                                    <span>Giảm giá <?= $booking->discount_percent  ?>%:</span>
                                                                    <span>-<?= number_format($booking->discount_amount) ?>đ</span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="payment-row total">
                                                                <span class="fw-bold">Tổng cộng:</span>
                                                                <span class="fw-bold text-purple"><?= number_format($booking->total_amount) ?>đ</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <?php
                                                    // Trong phần chi tiết đặt lịch, thêm các section sau:
                                                    $pets = Booking::getBookingPets($booking->id);
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

                                                    <?php
                                                    $services = Booking::getBookingServices($booking->id);
                                                    // 2. Chi tiết dịch vụ (từ BookingDetail)
                                                    if (!empty($services)): ?>
                                                        <div class="detail-section">
                                                            <h5><i class="fas fa-scissors"></i>Chi tiết dịch vụ</h5>
                                                            <div class="services-table">
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
                                                                            <tr class="pet-row" data-service-id="<?= $service['service_id'] ?>">
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

                                                    <?php
                                                    $staffSchedules = Booking::getStaffBookings($booking->id);

                                                    // dd($staffSchedules);

                                                    // 3. Lịch trình nhân viên (từ BookingStaffSchedule)
                                                    if (!empty($staffSchedules)): ?>
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
                                                                                    <?= date('d/m/Y', strtotime($booking->booking_date)) ?> -
                                                                                    <?= $item['time_slot']['start_time'] ?> đến <?= $item['time_slot']['end_time'] ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($reviews)): ?>
                                                        <div class="detail-section">
                                                            <h5><i class="fas fa-user-tie"></i>Đánh giá của bạn</h5>
                                                            <div class="staff-list">
                                                                <?php foreach ($reviews as $item): ?>
                                                                    <div class="staff-item">
                                                                        <div class="staff-info d-flex align-items-center">
                                                                            <div class="staff-avatar me-3">
                                                                                <img src="<?= show_avatar($user['avatar_url']) ?>" alt="<?= htmlspecialchars($user['name']) ?>">
                                                                            </div>

                                                                            <div class="staff-details">
                                                                                <div class="staff-name fw-bold"><?= htmlspecialchars($user['name']) ?></div>
                                                                                <div class="staff-role text-muted">
                                                                                    ⭐ <?= $item->rating ?>
                                                                                </div>

                                                                                Đánh giá: <?= htmlspecialchars($item->comment) ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif ?>

                                                    <?php if (!empty($booking->customer_notes)): ?>
                                                        <div class="detail-section">
                                                            <h5><i class="fas fa-comment"></i>Ghi chú khách hàng</h5>
                                                            <div class="detail-item">
                                                                <span><?= htmlspecialchars($booking->customer_notes) ?></span>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($booking->staff_notes)): ?>
                                                        <div class="detail-section">
                                                            <h5><i class="fas fa-user-tie"></i>Ghi chú nhân viên</h5>
                                                            <div class="detail-item">
                                                                <span><?= htmlspecialchars($booking->staff_notes) ?></span>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($booking->status  === 'cancelled' && !empty($booking->cancellation_reason)): ?>
                                                        <div class="detail-section">
                                                            <h5><i class="fas fa-ban"></i>Lý do hủy</h5>
                                                            <div class="detail-item">
                                                                <span><?= htmlspecialchars($booking->cancellation_reason) ?></span>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>


                                                    <?php
                                                    $transaction = Booking::getLatestVnPayTransaction($booking->id);

                                                    // 5. Thông tin thanh toán VNPay (nếu có)
                                                    if ($booking->payment_method === 'vnpay' && !empty($transaction)): ?>
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
                                                                        <span class="badge text-white <?= $transaction->status === 'success' ? 'bg-success' : 'bg-danger' ?>">
                                                                            <?= $transaction->status === 'success' ? 'Thành công' : 'Thất bại' ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else: ?>
                                    <tr class="pet-row">
                                        <td colspan="6" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Bạn chưa có lịch đặt nào</p>
                                                <a href="<?= base_url('/booking/create') ?>" class="btn btn-outline-purple">
                                                    <i class="fas fa-plus me-2"></i>Đặt lịch đầu tiên
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (!empty($metadata) && $pagination['last'] > 1): ?>
                        <div class="p-4 pagination-wrapper d-flex justify-content-between align-items-center mt-4">
                            <!-- Pagination Info -->
                            <div class="pagination-info text-muted">
                                Hiển thị <?= $pagination['from'] ?>
                                đến <?= $pagination['to'] ?>
                                trong tổng số <?= $pagination['total'] ?> đặt lịch
                            </div>

                            <!-- Pagination Links -->
                            <nav aria-label="Booking pagination">
                                <ul class="pagination custom-pagination mb-0">
                                    <!-- Previous Page -->
                                    <?php if ($pagination['current'] > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= base_url('/user/booking?page=' . ($pagination['current'] - 1)) ?>" aria-label="Previous">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-label="Previous">
                                                <i class="fas fa-chevron-left"></i>
                                            </span>
                                        </li>
                                    <?php endif ?>

                                    <!-- Page Numbers -->
                                    <?php
                                    $start = max(1, $pagination['current'] - 2);
                                    $end = min($pagination['last'], $pagination['current'] + 2);

                                    if ($end - $start < 4 && $pagination['last'] > 5) {
                                        $start = max(1, $end - 4);
                                    }

                                    if ($end - $start < 4 && $pagination['last'] > 5) {
                                        $end = min($pagination['last'], $start + 4);
                                    }
                                    ?>

                                    <!-- First page if not in range -->
                                    <?php if ($start > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= base_url('/user/booking?page=1') ?>">1</a>
                                        </li>
                                        <?php if ($start > 2): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        <?php endif ?>
                                    <?php endif ?>

                                    <!-- Page range -->
                                    <?php for ($i = $start; $i <= $end; $i++): ?>
                                        <li class="page-item <?= $i == $pagination['current'] ? 'active' : '' ?>">
                                            <?php if ($i == $pagination['current']): ?>
                                                <span class="page-link"><?= $i ?></span>
                                            <?php else: ?>
                                                <a class="page-link" href="<?= base_url('/user/booking?page=' . $i) ?>"><?= $i ?></a>
                                            <?php endif ?>
                                        </li>
                                    <?php endfor ?>

                                    <!-- Last page if not in range -->
                                    <?php if ($end < $pagination['last']): ?>
                                        <?php if ($end < $pagination['last'] - 1): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        <?php endif ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= base_url('/user/booking?page=' . $pagination['last']) ?>"><?= $pagination['last'] ?></a>
                                        </li>
                                    <?php endif ?>

                                    <!-- Next Page -->
                                    <?php if ($pagination['current'] < $pagination['last']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= base_url('/user/booking?page=' . ($pagination['current'] + 1)) ?>" aria-label="Next">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-label="Next">
                                                <i class="fas fa-chevron-right"></i>
                                            </span>
                                        </li>
                                    <?php endif ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php start_section('scripts') ?>
<script>
    // Toggle booking detail accordion
    document.querySelectorAll('.booking-detail-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const bookingId = this.getAttribute('data-booking-id');
            const detailRow = document.getElementById(`detail-${bookingId}`);
            const parentRow = this.closest('.booking-row');

            // Close all other details
            document.querySelectorAll('.booking-detail-row').forEach(row => {
                if (row.id !== `detail-${bookingId}`) {
                    row.classList.remove('show');
                }
            });

            // Remove active state from all buttons and rows
            document.querySelectorAll('.booking-detail-btn').forEach(b => {
                if (b !== this) {
                    b.classList.remove('active');
                }
            });

            document.querySelectorAll('.booking-row').forEach(row => {
                if (row !== parentRow) {
                    row.classList.remove('active');
                }
            });

            // Toggle current detail
            if (detailRow.classList.contains('show')) {
                detailRow.classList.remove('show');
                this.classList.remove('active');
                parentRow.classList.remove('active');
            } else {
                detailRow.classList.add('show');
                this.classList.add('active');
                parentRow.classList.add('active');
            }
        });
    });

    // Close booking detail function
    function closeBookingDetail(bookingId) {
        const detailRow = document.getElementById(`detail-${bookingId}`);
        const btn = document.querySelector(`[data-booking-id="${bookingId}"]`);
        const parentRow = btn.closest('.booking-row');

        detailRow.classList.remove('show');
        btn.classList.remove('active');
        parentRow.classList.remove('active');
    }

    // Cancel booking
    $('.cancel-btn').on('click', function(e) {
        e.preventDefault();
        const bookingId = $(this).data('booking-id');

        Swal.fire({
            title: 'Xác nhận hủy lịch',
            text: "Bạn có chắc chắn muốn hủy lịch đặt này?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Hủy lịch",
            cancelButtonText: "Không",
        }).then((result) => {
            if (result.isConfirmed) {
                // Show reason input
                Swal.fire({
                    title: 'Lý do hủy lịch',
                    input: 'textarea',
                    inputPlaceholder: 'Vui lòng cho biết lý do hủy lịch...',
                    inputAttributes: {
                        'aria-label': 'Lý do hủy lịch'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận hủy',
                    cancelButtonText: 'Không'
                }).then((reason) => {
                    if (reason.isConfirmed) {
                        loadAjaxStatus("start");
                        $.ajax({
                            url: `<?= base_url("/booking/quick-update/") ?>${bookingId}`,
                            method: 'POST',
                            data: {
                                action: 'cancel',
                                notes: reason.value || 'Khách hàng hủy lịch',
                                client: true
                            },
                            success: function(res) {
                                swAlert("Thông báo", res.message, "success");
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            },
                            error: function(err) {
                                swAlert("Thông báo", err.responseJSON.message, "error");
                            },
                            complete: function() {
                                loadAjaxStatus("stop");
                            },
                        });
                    }
                });
            }
        });
    });

    // Close detail when clicking on pet row (optional)
    document.querySelectorAll('.booking-row').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('.booking-detail-btn')) return;

            const bookingId = this.getAttribute('data-booking-id');
            const btn = this.querySelector('.booking-detail-btn');
            if (btn && !btn.classList.contains('active')) {
                btn.click();
            }
        });
    });

    // Payment button
    $('.payment-btn').on('click', function(e) {
        e.preventDefault();
        const bookingId = $(this).data('booking-id');

        // Redirect to payment page
        window.location.href = `<?= base_url('/payment/') ?>${bookingId}`;
    });

    // Filter functionality
    $('#applyFilter').on('click', function() {
        const status = $('#statusFilter').val();
        const paymentStatus = $('#paymentStatusFilter').val();
        const date = $('#dateFilter').val();

        let url = '<?= base_url('/user/booking') ?>';
        const params = new URLSearchParams();

        if (status) params.append('status', status);
        if (paymentStatus) params.append('payment_status', paymentStatus);
        if (date) params.append('date', date);

        if (params.toString()) {
            url += '?' + params.toString();
        }

        window.location.href = url;
    });

    // Clear filters
    function clearFilters() {
        $('#statusFilter').val('');
        $('#paymentStatusFilter').val('');
        $('#dateFilter').val('');
        window.location.href = '<?= base_url('/user/booking') ?>';
    }

    $('.review-btn').on('click', function(e) {
        e.preventDefault();
        const bookingId = $(this).data('booking-id');

        // Lấy danh sách dịch vụ đã sử dụng
        const services = [];
        $(this).closest('.pet-detail-content').find('.services-table tbody tr.pet-row').each(function() {
            const serviceId = $(this).data('service-id'); // Cần thêm data-service-id vào tr
            const serviceName = $(this).find('.service-name').text().trim();
            services.push({
                id: serviceId,
                name: serviceName
            });
        });

        showReviewModal(bookingId, services);
    });

    function showReviewModal(bookingId, services) {
        // Tạo HTML cho form đánh giá
        let servicesHtml = '';
        services.forEach(service => {
            servicesHtml += `
            <div class="service-review-item mb-3" data-service-id="${service.id}">
                <h6>${service.name}</h6>
                <div class="rating-container mb-2">
                    <label class="form-label">Đánh giá chất lượng:</label>
                    <div class="star-rating" data-service-id="${service.id}">
                        ${[1,2,3,4,5].map(star => 
                            `<i class="fas fa-star star-item" data-rating="${star}"></i>`
                        ).join('')}
                    </div>
                    <input type="hidden" name="rating_${service.id}" class="service-rating" value="5">
                </div>
                <div class="mb-2">
                    <label class="form-label">Nhận xét:</label>
                    <textarea class="form-control service-comment" name="comment_${service.id}" 
                              placeholder="Chia sẻ trải nghiệm của bạn về dịch vụ này..." rows="2"></textarea>
                </div>
            </div>
        `;
        });

        const reviewFormHtml = `
        <div class="review-form">
            <div class="mb-3">
                <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" id="isAnonymous">
                    Đánh giá ẩn danh
                </label>
            </div>
            ${servicesHtml}
        </div>
    `;

        Swal.fire({
            title: 'Đánh giá dịch vụ',
            html: reviewFormHtml,
            width: '600px',
            showCancelButton: true,
            confirmButtonText: 'Gửi đánh giá',
            cancelButtonText: 'Hủy',
            confirmButtonColor: '#6f42c1',
            allowOutsideClick: false, // Không cho phép đóng khi click bên ngoài
            didOpen: () => {
                // Khởi tạo star rating
                initStarRating();

                // Thêm real-time validation
                addRealTimeValidation(services);
            },
            preConfirm: () => {
                // Validate và collect data
                const result = collectReviewData(bookingId, services);

                if (result.error) {
                    Swal.showValidationMessage(result.error);
                    return false;
                }

                return result;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitReview(result.value);
            }
        });
    }

    // Thêm function validation thời gian thực
    function addRealTimeValidation(services) {
        // Validation cho service comments
        services.forEach(service => {
            $(`.service-comment[name="comment_${service.id}"]`).on('input', function() {
                const comment = $(this).val().trim();
                const $feedback = $(this).siblings('.validation-feedback');

                if ($feedback.length === 0) {
                    $(this).after('<div class="validation-feedback"></div>');
                }

                if (comment.length < 5) {
                    $(this).addClass('is-invalid').removeClass('is-valid');
                    $(this).siblings('.validation-feedback').text(`Cần ít nhất ${5 - comment.length} ký tự nữa`).show();
                } else {
                    $(this).addClass('is-valid').removeClass('is-invalid');
                    $(this).siblings('.validation-feedback').hide();
                }
            });
        });
    }

    // Validation cho ratings
    $('.star-rating .star-item').on('click', function() {
        const $container = $(this).closest('.star-rating');
        $container.removeClass('invalid-rating');
        $container.siblings('.rating-error').remove();
    });

    function initStarRating() {
        $('.star-rating').each(function() {
            const $container = $(this);
            const $stars = $container.find('.star-item');

            // Set initial rating (5 stars)
            $stars.addClass('active');

            $stars.on('click', function() {
                const rating = $(this).data('rating');
                const serviceId = $container.data('service-id');

                // Update visual stars
                $stars.removeClass('active');
                $stars.slice(0, rating).addClass('active');

                // Update hidden input
                if (serviceId) {
                    $(`.service-rating[name="rating_${serviceId}"]`).val(rating);
                } else {
                    $('#overallRating').val(rating);
                }
            });

            $stars.on('mouseenter', function() {
                const rating = $(this).data('rating');
                $stars.removeClass('hover');
                $stars.slice(0, rating).addClass('hover');
            });

            $container.on('mouseleave', function() {
                $stars.removeClass('hover');
            });
        });
    }

    function collectReviewData(bookingId, services) {
        const reviewData = {
            booking_id: bookingId,
            is_anonymous: $('#isAnonymous').is(':checked') ? 1 : 0,
            services: []
        };

        // Collect and validate service reviews
        let missingServices = [];
        let invalidRatings = [];
        let missingComments = [];

        services.forEach(service => {
            const rating = $(`.service-rating[name="rating_${service.id}"]`).val();
            const comment = $(`.service-comment[name="comment_${service.id}"]`).val().trim();

            // Check if rating exists and is valid
            if (!rating || rating < 1 || rating > 5) {
                invalidRatings.push(service.name);
                return;
            }

            // Check if comment exists and has minimum length
            if (!comment || comment.length < 5) {
                missingComments.push(service.name);
                return;
            }

            reviewData.services.push({
                service_id: service.id,
                rating: parseInt(rating),
                comment: comment
            });
        });

        // Validation errors
        if (invalidRatings.length > 0) {
            return {
                error: `Vui lòng chọn đánh giá cho dịch vụ: ${invalidRatings.join(', ')}`
            };
        }

        if (missingComments.length > 0) {
            return {
                error: `Vui lòng nhập nhận xét cho dịch vụ: ${missingComments.join(', ')} (ít nhất 5 ký tự)`
            };
        }

        // Check if all services have been reviewed
        if (reviewData.services.length !== services.length) {
            return {
                error: 'Vui lòng hoàn thành đánh giá cho tất cả dịch vụ đã sử dụng'
            };
        }

        return reviewData;
    }

    function submitReview(reviewData) {
        loadAjaxStatus("start");
        $.ajax({
            url: '<?= base_url("/booking/review") ?>',
            method: 'POST',
            data: JSON.stringify(reviewData),
            dataType: 'json',
            contentType: 'application/json',
            success: function(response) {
                Swal.fire({
                    title: 'Thành công!',
                    text: 'Cảm ơn bạn đã đánh giá dịch vụ của chúng tôi!',
                    icon: 'success',
                    confirmButtonColor: '#6f42c1'
                }).then(() => {
                    // Disable review button và show đã đánh giá
                    const reviewBtn = $(`.review-btn[data-booking-id="${reviewData.booking_id}"]`);
                    reviewBtn.removeClass('review-btn')
                        .addClass('btn-success')
                        .prop('disabled', true)
                        .html('<i class="fas fa-check me-1"></i>Đã đánh giá');

                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                });
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Có lỗi xảy ra khi gửi đánh giá';
                Swal.fire({
                    title: 'Lỗi!',
                    text: message,
                    icon: 'error',
                    confirmButtonColor: '#6f42c1'
                });
            },
            complete: function() {
                loadAjaxStatus("stop");
            }
        });
    }
</script>


<?php end_section() ?>
<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Start: Khối chức năng -->
<div class="controls-header">
  <form class="admin-controls__search-form">
    <input type="text" name="q" placeholder="Tìm kiếm booking..." value="<?= $searching ?>" class="admin-controls__search-input">

    <select name="status" class="admin-controls__select">
      <option value="">Tất cả trạng thái</option>
      <?php foreach ($statuses as $key => $status): ?>
        <option value="<?= $key ?>" <?= $statusFilter === $key ? 'selected' : '' ?>><?= $status ?></option>
      <?php endforeach ?>
    </select>

    <select name="staff_id" class="admin-controls__select">
      <option value="">Tất cả nhân viên</option>
      <?php foreach ($staffList as $staff): ?>
        <option value="<?= $staff->user_id ?>" <?= $staffFilter == $staff->user_id ? 'selected' : '' ?>><?= $staff->name ?></option>
      <?php endforeach ?>
    </select>

    <input type="date" name="date" value="<?= $dateFilter ?>" class="admin-controls__input">

    <select name="payment_status" class="admin-controls__select">
      <option value="">Tất cả thanh toán</option>
      <?php foreach ($paymentStatuses as $key => $paymentStatus): ?>
        <option value="<?= $key ?>" <?= $paymentStatusFilter === $key ? 'selected' : '' ?>><?= $paymentStatus ?></option>
      <?php endforeach ?>
    </select>

    <button type="submit" class="btn btn--secondary">Lọc</button>
    <a href="<?= base_url('/admin/booking') ?>" class="btn btn--gray">Reset</a>
  </form>
</div>
<!-- End: Khối chức năng -->

<!-- Start: Bảng dữ liệu -->
<section class="admin-table-wrapper">
  <h3 class="admin-table__title">Danh sách booking</h3>

  <table class="admin-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Mã booking</th>
        <th>Khách hàng</th>
        <th>Nhân viên</th>
        <th>Ngày thực hiện</th>
        <th>Tổng tiền</th>
        <th>Trạng thái</th>
        <th>Thanh toán</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php if (isset($metadata) && count($metadata) > 0): ?>
        <?php $index = ($pagination['current'] - 1) * 10 + 1; ?>
        <?php foreach ($metadata as $item): ?>
          <tr>
            <td><?= $index ?></td>
            <td>
              <strong><?= htmlspecialchars($item['booking_code']) ?></strong>
            </td>
            <td>
              <div>
                <strong><?= htmlspecialchars($item['customer_name']) ?></strong><br>
                <small><?= htmlspecialchars($item['customer_email']) ?></small>
              </div>
            </td>
            <td>
              <div>
                <strong><?= htmlspecialchars($item['staff_name']) ?></strong>
              </div>
            </td>
            <td><?= format_date($item['booking_date']) ?></td>
            <td>
              <strong class="text-primary"><?= number_format($item['total_amount']) ?> VND</strong>
            </td>
            <td>
              <?php
              $statusClass = '';
              switch ($item['status']) {
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
              <span class="badge <?= $statusClass ?>"><?= $statuses[$item['status']] ?? $item['status'] ?></span>
            </td>
            <td>
              <?php
              $paymentClass = '';
              switch ($item['payment_status']) {
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
              <span class="badge <?= $paymentClass ?>"><?= $paymentStatuses[$item['payment_status']] ?? $item['payment_status'] ?></span>
            </td>
            <td>
              <div class="action-buttons">
                <a href="<?= base_url('/admin/booking/' . $item['id']) ?>">
                  <button class="action-btn view" title="Xem chi tiết">
                    <i class="fa-solid fa-eye"></i>
                  </button>
                </a>
                <?php if (in_array($item['status'], ['pending', 'cancelled'])): ?>
                  <button class="action-btn delete delete-item" title="Xóa"
                    data-name="<?= $item['booking_code'] ?>"
                    data-id="<?= $item['id'] ?>">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                <?php endif; ?>

                <?php if ($item['status'] === 'pending'): ?>
                  <button class="action-btn success quick-action" title="Xác nhận"
                    data-id="<?= $item['id'] ?>" data-action="accept">
                    <i class="fa-solid fa-check"></i>
                  </button>
                  <button class="action-btn danger quick-action" title="Từ chối"
                    data-id="<?= $item['id'] ?>" data-action="reject">
                    <i class="fa-solid fa-times"></i>
                  </button>
                <?php elseif ($item['status'] === 'confirmed'): ?>
                  <button class="action-btn success quick-action" title="Hoàn thành"
                    data-id="<?= $item['id'] ?>" data-action="in_progress">
                    <i class="fa-solid fa-play"></i>
                  </button>
                <?php elseif ($item['status'] === 'in_progress'): ?>
                  <button class="action-btn success quick-action" title="Hoàn thành"
                    data-id="<?= $item['id'] ?>" data-action="finish">
                    <i class="fa-solid fa-check-double"></i>
                  </button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php $index++; ?>
        <?php endforeach ?>
      <?php else: ?>
        <tr>
          <td colspan="10" align="center">Chưa có dữ liệu</td>
        </tr>
      <?php endif ?>
    </tbody>
  </table>
</section>
<!-- End: Bảng dữ liệu -->

<!-- Start: Phân trang -->
<?php include_partial('admin/pagination', ['pagination' => $pagination]) ?>
<!-- End: Phân trang -->

<div class="badge-legend mt-2">
  <h3 class="badge-legend__title">Bảng Màu Sắc Trạng Thái</h3>

  <div class="badge-legend__grid">
    <!-- Booking Status Section -->
    <div class="badge-legend__section">
      <h4 class="badge-legend__section-title">🏷️ Trạng Thái Booking</h4>
      <div class="badge-legend__items">
        <div class="badge-legend__item">
          <span class="badge badge--warning">CHỜ XÁC NHẬN</span>
          <div>
            <span>Chờ xác nhận</span>
            <div class="badge-legend__description">Booking vừa được tạo, đang chờ admin xác nhận</div>
          </div>
        </div>

        <div class="badge-legend__item">
          <span class="badge badge--info">ĐÃ XÁC NHẬN</span>
          <div>
            <span>Đã xác nhận</span>
            <div class="badge-legend__description">Booking đã được xác nhận, sẵn sàng thực hiện</div>
          </div>
        </div>

        <div class="badge-legend__item">
          <span class="badge badge--progress">Đang thực hiện</span>
          <div>
            <span>Đang thực hiện</span>
            <div class="badge-legend__description">Dịch vụ đang được thực hiện</div>
          </div>
        </div>

        <div class="badge-legend__item">
          <span class="badge badge--success">ĐÃ HOÀN THÀNH</span>
          <div>
            <span>Đã hoàn thành</span>
            <div class="badge-legend__description">Dịch vụ đã được thực hiện xong</div>
          </div>
        </div>

        <div class="badge-legend__item">
          <span class="badge badge--danger">ĐÃ HUỶ</span>
          <div>
            <span>Đã hủy</span>
            <div class="badge-legend__description">Booking đã bị hủy bởi khách hàng hoặc admin</div>
          </div>
        </div>

        <div class="badge-legend__item">
          <span class="badge badge--secondary">Không đến</span>
          <div>
            <span>Không đến</span>
            <div class="badge-legend__description">Khách hàng không đến sử dụng dịch vụ</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Status Section -->
    <div class="badge-legend__section badge-legend__section--payment">
      <h4 class="badge-legend__section-title">💳 Trạng Thái Thanh Toán</h4>
      <div class="badge-legend__items">
        <div class="badge-legend__item">
          <span class="badge badge--warning">CHỜ THANH TOÁN</span>
          <div>
            <span>Chờ thanh toán</span>
            <div class="badge-legend__description">Khách hàng chưa thanh toán</div>
          </div>
        </div>

        <div class="badge-legend__item">
          <span class="badge badge--success">ĐÃ THANH TOÁN</span>
          <div>
            <span>Đã thanh toán</span>
            <div class="badge-legend__description">Đã nhận được thanh toán từ khách hàng</div>
          </div>
        </div>

        <div class="badge-legend__item">
          <span class="badge badge--danger">THANH TOÁN THẤT BẠI</span>
          <div>
            <span>Thanh toán thất bại</span>
            <div class="badge-legend__description">Có lỗi xảy ra trong quá trình thanh toán</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<?php start_section('scripts') ?>
<script>
  $(document).ready(function() {
    // Xóa booking
    $('.delete-item').on('click', function(e) {
      e.preventDefault();
      const id = $(this).data('id');
      const name = $(this).data('name');

      Swal.fire({
        title: `Bạn có chắc chắn muốn xóa booking <b>'${name}'</b> này?`,
        text: "Hành động này không thể hoàn tác!",
        icon: "warning",
        dangerMode: true,
        showCloseButton: true,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Xóa",
        cancelButtonText: "Hủy",
      }).then((willDelete) => {
        if (willDelete.isConfirmed) {
          $.ajax({
            url: `<?= base_url('/admin/booking/') ?>${id}`,
            method: 'DELETE',
            success: function(res) {
              swAlert("Thông báo", res.message, "success");
              setTimeout(() => {
                window.location.reload();
              }, 1500);
            },
            error: function(err) {
              swAlert("Thông báo", err.responseJSON.message, "error");
            }
          });
        }
      });
    });

    // Cập nhật nhanh trạng thái
    $('.quick-action').on('click', function(e) {
      e.preventDefault();
      const id = $(this).data('id');
      const action = $(this).data('action');

      let title = '';
      let text = '';

      switch (action) {
        case 'accept':
          title = 'Xác nhận booking';
          text = 'Bạn có chắc chắn muốn xác nhận booking này?';
          break;
        case 'in_progress':
          title = 'Đang thực hiện booking';
          text = 'Bạn có chắc chắn muốn đánh dấu booking này đang được thực hiện?';
          break;
        case 'reject':
          title = 'Từ chối booking';
          text = 'Bạn có chắc chắn muốn từ chối booking này?';
          break;
        case 'finish':
          title = 'Hoàn thành booking';
          text = 'Xác nhận booking đã hoàn thành?';
          break;
      }

      Swal.fire({
        title: title,
        text: text,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Xác nhận",
        cancelButtonText: "Hủy",
        input: 'textarea',
        inputPlaceholder: 'Ghi chú (tùy chọn)...'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: `<?= base_url('/admin/booking/quick-update/') ?>${id}`,
            method: 'POST',
            data: {
              action: action,
              notes: result.value || ''
            },
            success: function(res) {
              swAlert("Thông báo", res.message, "success");
              setTimeout(() => {
                window.location.reload();
              }, 1500);
            },
            error: function(err) {
              swAlert("Thông báo", err.responseJSON.message, "error");
            }
          });
        }
      });
    });
  });
</script>
<?php end_section() ?>

<?php start_section('links') ?>
<style>
  /* Admin Booking Controls Styles */
  .controls-header {
    background: #ffffff;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 24px;
    border: 1px solid #e5e7eb;
  }

  /* Search Form Layout */
  .admin-controls__search-form {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
    margin-top: 16px;
  }

  /* Input Styles */
  .admin-controls__search-input,
  .admin-controls__input,
  .admin-controls__select {
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    background-color: #ffffff;
    transition: all 0.2s ease;
    min-width: 0;
  }

  .admin-controls__search-input {
    flex: 1;
    min-width: 200px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 40px;
  }

  .admin-controls__select {
    min-width: 160px;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
  }

  .admin-controls__input {
    width: 150px;
  }

  /* Focus States */
  .admin-controls__search-input:focus,
  .admin-controls__input:focus,
  .admin-controls__select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  /* Hover States */
  .admin-controls__search-input:hover,
  .admin-controls__input:hover,
  .admin-controls__select:hover {
    border-color: #d1d5db;
  }

  /* Button Styles */
  .btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    white-space: nowrap;
  }

  .btn--primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
  }

  .btn--primary:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
    transform: translateY(-1px);
  }

  .btn--secondary {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
  }

  .btn--secondary:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    transform: translateY(-1px);
  }

  .btn--gray {
    background: #f8fafc;
    color: #64748b;
    border: 2px solid #e2e8f0;
  }

  .btn--gray:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #475569;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .controls-header {
      padding: 16px;
    }

    .admin-controls__search-form {
      flex-direction: column;
      align-items: stretch;
    }

    .admin-controls__search-input,
    .admin-controls__select,
    .admin-controls__input {
      width: 100%;
      min-width: auto;
    }

    .btn {
      justify-content: center;
      width: 100%;
    }
  }

  @media (max-width: 480px) {
    .controls-header {
      padding: 12px;
      margin-bottom: 16px;
    }

    .admin-controls__search-form {
      gap: 8px;
    }

    .btn {
      padding: 12px 16px;
      font-size: 16px;
    }
  }

  /* Additional Enhancement */
  .controls-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #3b82f6 0%, #10b981 50%, #f59e0b 100%);
    border-radius: 12px 12px 0 0;
  }

  .controls-header {
    position: relative;
  }

  /* Animation for smooth interactions */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(10px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .controls-header {
    animation: fadeInUp 0.3s ease-out;
  }

  /* Custom scrollbar for select dropdowns */
  .admin-controls__select::-webkit-scrollbar {
    width: 6px;
  }

  .admin-controls__select::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
  }

  .admin-controls__select::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
  }

  .admin-controls__select::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
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

  /* Badge Color Legend */
  .badge-legend {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 24px;
    border: 1px solid #e5e7eb;
  }

  .badge-legend__title {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .badge-legend__title::before {
    content: '🎨';
    font-size: 18px;
  }

  .badge-legend__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
  }

  .badge-legend__section {
    background: #f8fafc;
    padding: 16px;
    border-radius: 8px;
    border-left: 4px solid #3b82f6;
  }

  .badge-legend__section-title {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .badge-legend__items {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .badge-legend__item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #4b5563;
  }

  /* Responsive Badge Legend */
  @media (max-width: 768px) {
    .badge-legend__grid {
      grid-template-columns: 1fr;
    }

    .badge-legend__items {
      flex-direction: column;
      align-items: flex-start;
    }
  }

  /* Animation for badge appearance */
  @keyframes badgeSlideIn {
    from {
      opacity: 0;
      transform: translateX(-10px);
    }

    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  .badge {
    animation: badgeSlideIn 0.3s ease-out;
  }

  /* Badge with icon support */
  .badge--with-icon {
    padding-left: 8px;
  }

  .badge--with-icon::after {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    margin-left: 6px;
    opacity: 0.7;
  }
</style>
<?php end_section() ?>
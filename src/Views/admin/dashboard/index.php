<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Start: Grid Thống kê + Biểu đồ -->
<div class="admin-overview-grid">
  <!-- Start: Các thống kê -->
  <div class="admin-overview__left">
    <!-- Start: Tổng số khách hàng -->
    <div class="admin-card">
      <!-- Tiêu đề -->
      <p class="admin-card__label">Tổng khách hàng</p>
      <!-- Nhóm icon + số liệu -->
      <div class="admin-card__content">
        <div class="admin-card__icon">
          <i class="fa-solid fa-user"></i>
        </div>
        <p class="admin-card__value"><?= number_format($stats['total_customers']) ?></p>
      </div>
    </div>
    <!-- End: Tổng số khách hàng -->

    <!-- Start: Tổng lịch hẹn -->
    <div class="admin-card">
      <!-- Tiêu đề -->
      <p class="admin-card__label">Số lịch hẹn</p>
      <!-- Icon + Giá trị -->
      <div class="admin-card__content">
        <div class="admin-card__icon">
          <i class="fa-solid fa-calendar-days"></i>
        </div>
        <p class="admin-card__value"><?= number_format($stats['total_bookings']) ?></p>
      </div>
    </div>
    <!-- End: Tổng lịch hẹn -->

    <!-- Start: Số người đang sử dụng dịch vụ -->
    <div class="admin-card">
      <!-- Tiêu đề -->
      <p class="admin-card__label">Đang sử dụng DV</p>
      <!-- Icon + Giá trị -->
      <div class="admin-card__content">
        <div class="admin-card__icon">
          <i class="fa-solid fa-paw"></i>
        </div>
        <p class="admin-card__value"><?= number_format($stats['in_progress_bookings']) ?></p>
      </div>
    </div>
    <!-- End: Số người đang sử dụng dịch vụ -->

    <!-- Start: Số người đã sử dụng dịch vụ -->
    <div class="admin-card">
      <!-- Tiêu đề -->
      <p class="admin-card__label">Đã sử dụng DV</p>
      <!-- Icon + Giá trị -->
      <div class="admin-card__content">
        <div class="admin-card__icon">
          <i class="fa-solid fa-envelope-open-text"></i>
        </div>
        <p class="admin-card__value"><?= number_format($stats['completed_bookings']) ?></p>
      </div>
    </div>
    <!-- End: Số người đã sử dụng dịch vụ -->
  </div>
  <!-- End: Các thẻ thống kê -->

  <!-- Start: Biểu đồ tròn -->
  <div class="admin-overview__right">
    <!-- Start: Biểu đồ doanh thu -->
    <div class="admin-chart">
      <!-- Start: Nhãn + Bộ lọc -->
      <div class="admin-chart__header">
        <p class="admin-chart__label">Doanh thu</p>
        <select class="admin-chart__select" id="revenueFilter">
          <option value="week">Tuần</option>
          <option value="month">Tháng</option>
          <option value="year">Năm</option>
        </select>
      </div>
      <!-- End: Nhãn + Bộ lọc -->

      <!-- Start: Vòng tròn biểu đồ -->
      <div class="chart-container">
        <div class="donut-chart">
          <svg width="150" height="150">
            <defs>
              <linearGradient id="revenueGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#f59e0b;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#d97706;stop-opacity:1" />
              </linearGradient>
            </defs>
            <circle cx="75" cy="75" r="60" stroke="#f1f5f9" stroke-width="12" fill="none" />
            <circle
              id="revenueCircle"
              cx="75" cy="75" r="60"
              stroke="url(#revenueGradient)"
              stroke-width="12"
              fill="none"
              stroke-dasharray="377"
              stroke-dashoffset="<?= 377 - (377 * (100 - $revenueStats['revenue_percentage']) / 100) ?>"
              stroke-linecap="round" />
          </svg>
          <div class="donut-percentage" id="revenuePercentage"><?= $revenueStats['revenue_percentage'] ?>%</div>
        </div>
      </div>
      <!-- End: Vòng tròn biểu đồ -->

      <!-- Hiển thị doanh thu số -->
      <div class="chart-info">
        <p class="chart-amount"><?= number_format($revenueStats['period_revenue']) ?> VNĐ</p>
        <p class="chart-total">/ <?= number_format($revenueStats['total_revenue']) ?> VNĐ tổng</p>
      </div>
    </div>
    <!-- End: Biểu đồ doanh thu -->

    <!-- Start: Biểu đồ Dịch vụ -->
    <div class="admin-chart">
      <!-- Start: Nhãn + Bộ lọc -->
      <div class="admin-chart__header">
        <p class="admin-chart__label">Dịch vụ</p>
        <select class="admin-chart__select" id="serviceFilter">
          <option value="all">Tất cả</option>
          <option value="used">Đã sử dụng</option>
        </select>
      </div>
      <!-- End: Nhãn + Bộ lọc -->

      <!-- Vòng tròn biểu đồ -->
      <div class="chart-container">
        <div class="donut-chart">
          <svg width="150" height="150">
            <defs>
              <linearGradient id="serviceGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#16a34a;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#15803d;stop-opacity:1" />
              </linearGradient>
            </defs>
            <circle cx="75" cy="75" r="60" stroke="#f1f5f9" stroke-width="12" fill="none" />
            <circle
              id="serviceCircle"
              cx="75" cy="75" r="60"
              stroke="url(#serviceGradient)"
              stroke-width="12"
              fill="none"
              stroke-dasharray="377"
              stroke-dashoffset="<?= 377 - (377 * (100 - $serviceStats['service_usage_percentage']) / 100) ?>"
              stroke-linecap="round" />
          </svg>
          <div class="donut-percentage" id="servicePercentage"><?= $serviceStats['service_usage_percentage'] ?>%</div>
        </div>
      </div>
      <!-- End: Vòng tròn biểu đồ -->

      <!-- Hiển thị thông tin dịch vụ -->
      <div class="chart-info">
        <p class="chart-amount"><?= number_format($serviceStats['used_services']) ?> DV đã dùng</p>
        <p class="chart-total">/ <?= number_format($serviceStats['total_services']) ?> DV tổng</p>
      </div>
    </div>
    <!-- End: Biểu đồ Dịch vụ -->
  </div>
  <!-- End: Biểu đồ tròn -->
</div>
<!-- End: Grid Thống kê + Biểu đồ -->

<!-- Start: Bảng danh sách lịch hẹn -->
<h3 class="">Danh sách lịch hẹn gần nhất</h3>

<table class="admin-table mt-1">
  <thead>
    <tr>
      <th>#</th>
      <th>Tên dịch vụ</th>
      <th>Khách hàng</th>
      <th>Nhân viên</th>
      <th>Giá tiền</th>
      <th>Trạng thái</th>
      <th>Ngày thực hiện</th>
      <th>Ngày tạo lịch</th>
      <th>Hành động</th>
    </tr>
  </thead>

  <tbody data-type="appointment">
    <?php if (!empty($recentBookings)): ?>
      <?php foreach ($recentBookings as $index => $booking): ?>
        <tr>
          <td><?= $index + 1 ?></td>
          <td><?= htmlspecialchars($booking['service_name'] ?? 'N/A') ?></td>
          <td>
            <?= htmlspecialchars($booking['customer_name'] ?? 'N/A') ?>
            <br>
            <span class="admin-table__email">(<?= htmlspecialchars($booking['customer_email'] ?? 'N/A') ?>)</span>
          </td>
          <td>
            <?= htmlspecialchars($booking['staff_name'] ?? 'Chưa phân công') ?>
            <br>
            <span class="admin-table__code">(<?= htmlspecialchars($booking['staff_code'] ?? 'N/A') ?>)</span>
          </td>
          <td><?= number_format($booking['total_amount'] ?? 0) ?> đ</td>
          <td>
            <?php
            $statusClass = '';
            $statusText = '';
            switch ($booking['status']) {
              case 'pending':
                $statusClass = 'status--pending';
                $statusText = 'Đang chờ...';
                break;
              case 'in_progress':
                $statusClass = 'status--inprogress';
                $statusText = 'Đang thực hiện...';
                break;
              case 'completed':
                $statusClass = 'status--success';
                $statusText = 'Đã hoàn thành';
                break;
              case 'cancelled':
                $statusClass = 'status--danger';
                $statusText = 'Đã hủy';
                break;
              case 'confirmed':
                $statusClass = 'status--info';
                $statusText = 'Đã xác nhận';
                break;
              default:
                $statusClass = 'status--pending';
                $statusText = 'Không xác định';
            }
            ?>
            <span class="status <?= $statusClass ?>"><?= $statusText ?></span>
          </td>
          <td><?= date('d/m/Y', strtotime($booking['booking_date'] ?? '')) ?></td>
          <td><?= date('d/m/Y H:i', strtotime($booking['created_at'] ?? '')) ?></td>
          <td>
            <?php if ($booking['status'] == 'pending'): ?>
              <button class="btn btn--primary" data-action="accept" data-id="<?= $booking['id'] ?>">Duyệt</button>
            <?php elseif ($booking['status'] == 'confirmed'): ?>
              <button class="btn btn--info" data-action="in_progress" data-id="<?= $booking['id'] ?>">Bắt đầu</button>
            <?php elseif ($booking['status'] == 'in_progress'): ?>
              <button class="btn btn--success" data-action="finish" data-id="<?= $booking['id'] ?>">Hoàn thành</button>
            <?php else: ?>
              <button class="btn btn--secondary" disabled>Đã xử lý</button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="9" class="text-center">Chưa có lịch hẹn nào</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<!-- Script -->
<?php start_section('scripts') ?>
<script src="<?= base_url('cms/assets/js/components/admin-modal-handler.js') ?>"></script>

<script>
  // Dữ liệu từ PHP
  const dashboardData = {
    revenuePercentage: <?= $revenueStats['revenue_percentage'] ?>,
    servicePercentage: <?= $serviceStats['service_usage_percentage'] ?>,
    stats: <?= json_encode($stats) ?>,
    revenueStats: <?= json_encode($revenueStats) ?>,
    serviceStats: <?= json_encode($serviceStats) ?>
  };

  // Animation cho biểu đồ khi page load
  window.addEventListener('load', function() {
    console.log('Dashboard loaded, initializing charts...');

    // Kiểm tra dữ liệu
    console.log('Dashboard data:', dashboardData);

    // Set transition cho tất cả circles
    const circles = document.querySelectorAll('circle[stroke-dasharray]');
    circles.forEach(circle => {
      circle.style.transition = 'stroke-dashoffset 1s cubic-bezier(0.4, 0, 0.2, 1), stroke-width 0.3s ease, opacity 0.3s ease';
    });

    // Animate counters
    const counters = document.querySelectorAll('.admin-card__value');
    counters.forEach((counter, index) => {
      const target = parseInt(counter.textContent.replace(/,/g, '')) || 0;
      let current = 0;
      const increment = target / 50;

      const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
          counter.textContent = target.toLocaleString();
          clearInterval(timer);
        } else {
          counter.textContent = Math.floor(current).toLocaleString();
        }
      }, 30 + (index * 10));
    });

    // Animate charts với delay
    setTimeout(() => {
      const revenuePercentage = dashboardData.revenuePercentage || 0;
      animateChart('revenueCircle', revenuePercentage);
      console.log('Revenue chart animated:', revenuePercentage);
    }, 600);

    setTimeout(() => {
      const servicePercentage = dashboardData.servicePercentage || 0;
      animateChart('serviceCircle', servicePercentage);
      console.log('Service chart animated:', servicePercentage);
    }, 900);
  });

  // Function to animate donut charts
  function animateChart(circleId, percentage) {
    const circle = document.getElementById(circleId);
    if (!circle) {
      console.error(`Circle with id ${circleId} not found`);
      return;
    }

    const circumference = 377;

    console.log(`Animate ${circleId}: ${percentage}%`);

    // Xử lý trường hợp 0%
    if (percentage === 0 || percentage === null || percentage === undefined) {
      circle.style.transition = 'stroke-dashoffset 0.8s ease-out, opacity 0.3s ease';
      circle.style.strokeDashoffset = circumference; // Ẩn hoàn toàn
      circle.style.opacity = '0.15';
      circle.closest('.admin-chart').classList.add('chart-empty');
      return;
    }

    // Bỏ class empty nếu có dữ liệu
    circle.closest('.admin-chart').classList.remove('chart-empty');

    // CÔNG THỨC ĐÚNG: Tính offset để hiển thị đúng phần trăm
    // Với 20%, ta muốn hiển thị 20% của vòng tròn
    // stroke-dashoffset = circumference - (circumference * percentage / 100)
    const targetOffset = circumference - (circumference * percentage / 100);

    // Animation mượt
    circle.style.transition = 'stroke-dashoffset 1.2s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease';
    circle.style.strokeDashoffset = targetOffset;
    circle.style.opacity = '1';

    console.log(`Setting offset: ${targetOffset} (${percentage}% of ${circumference})`);
  }

  // Sửa lại hàm updateRevenueChart
  function updateRevenueChart(data) {
    let totalRevenue = 0;

    if (Array.isArray(data) && data.length > 0) {
      totalRevenue = data.reduce((sum, item) => {
        const revenue = parseFloat(item.revenue || 0);
        return sum + (isNaN(revenue) ? 0 : revenue);
      }, 0);
    }

    const maxPossible = dashboardData.revenueStats?.total_revenue || 1;
    const newPercentage = maxPossible > 0 ? Math.round((totalRevenue / maxPossible) * 100) : 0;

    console.log(`Revenue update: ${totalRevenue}/${maxPossible} = ${newPercentage}%`);

    // Animate chart với phần trăm đúng
    animateChart('revenueCircle', newPercentage);

    // Cập nhật text
    const percentageElement = document.getElementById('revenuePercentage');
    if (percentageElement) {
      percentageElement.textContent = newPercentage + '%';
    }

    // Cập nhật thông tin chi tiết
    const chartInfo = document.querySelector('#revenueCircle')?.closest('.admin-chart')?.querySelector('.chart-info');
    if (chartInfo) {
      const amountEl = chartInfo.querySelector('.chart-amount');
      const totalEl = chartInfo.querySelector('.chart-total');

      if (amountEl) {
        amountEl.textContent = totalRevenue.toLocaleString() + ' VNĐ';
      }
      if (totalEl) {
        totalEl.textContent = '/ ' + maxPossible.toLocaleString() + ' VNĐ tổng';
      }
    }
  }

  // Sửa lại hàm updateServiceChart
  function updateServiceChart(data) {
    let usedServices = 0;

    if (Array.isArray(data) && data.length > 0) {
      usedServices = data.filter(item => {
        const usageCount = parseInt(item.usage_count || 0);
        return !isNaN(usageCount) && usageCount > 0;
      }).length;
    }

    const totalServices = dashboardData.serviceStats?.total_services || 1;
    const newPercentage = totalServices > 0 ? Math.round((usedServices / totalServices) * 100) : 0;

    console.log(`Service update: ${usedServices}/${totalServices} = ${newPercentage}%`);

    // Animate chart với phần trăm đúng
    animateChart('serviceCircle', newPercentage);

    // Cập nhật text
    const percentageElement = document.getElementById('servicePercentage');
    if (percentageElement) {
      percentageElement.textContent = newPercentage + '%';
    }

    // Cập nhật thông tin chi tiết
    const chartInfo = document.querySelector('#serviceCircle')?.closest('.admin-chart')?.querySelector('.chart-info');
    if (chartInfo) {
      const amountEl = chartInfo.querySelector('.chart-amount');
      const totalEl = chartInfo.querySelector('.chart-total');

      if (amountEl) {
        amountEl.textContent = usedServices.toLocaleString() + ' DV đã dùng';
      }
      if (totalEl) {
        totalEl.textContent = '/ ' + totalServices.toLocaleString() + ' DV tổng';
      }
    }
  }

  // Easing function cho animation mượt
  function easeOutCubic(t) {
    return 1 - Math.pow(1 - t, 3);
  }


  // Handle filter changes
  document.getElementById('revenueFilter').addEventListener('change', function() {
    const period = this.value;

    // Show loading state
    const chart = this.closest('.admin-chart');
    chart.style.opacity = '0.7';

    // Fetch new data
    fetch(`/admin/dashboard/revenue-chart?period=${period}`)
      .then(response => response.json())
      .then(data => {
        // Update chart with new data
        updateRevenueChart(data);
        chart.style.opacity = '1';
      })
      .catch(error => {
        console.error('Error fetching revenue data:', error);
        chart.style.opacity = '1';
      });
  });

  document.getElementById('serviceFilter').addEventListener('change', function() {
    const filter = this.value;

    // Show loading state
    const chart = this.closest('.admin-chart');
    chart.style.opacity = '0.7';

    // Fetch new data
    fetch(`/admin/dashboard/service-chart?filter=${filter}`)
      .then(response => response.json())
      .then(data => {
        // Update chart with new data
        updateServiceChart(data);
        chart.style.opacity = '1';
      })
      .catch(error => {
        console.error('Error fetching service data:', error);
        chart.style.opacity = '1';
      });
  });



  // Handle booking actions
  document.querySelectorAll('[data-action]').forEach(btn => {
    btn.addEventListener('click', function() {
      const action = this.dataset.action;
      const bookingId = this.dataset.id;
      // const modal = document.getElementById('approvalModal');

      // Show modal with appropriate message
      let actionText = '';
      let title = '';
      switch (action) {
        case 'accept':
          title = 'Xác nhận booking';
          actionText = 'duyệt lịch hẹn';
          break;
        case 'in_progress':
          title = 'Bắt đầu dịch vụ';
          actionText = 'bắt đầu dịch vụ';
          break;
        case 'finish':
          title = 'Hoàn thành booking';
          actionText = 'hoàn thành dịch vụ';
          break;
      }


      // $('.modal-action-label').text(`Bạn có chắc chắn muốn ${actionText} cho `);
      // modal.querySelector('#modalTargetName').textContent = `Booking #${bookingId}`;
      // modal.style.display = 'flex';

      Swal.fire({
        title: title,
        text: actionText,
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
            url: `<?= base_url('/admin/booking/quick-update/') ?>${bookingId}`,
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

      // Handle confirm action
      // modal.querySelector('#confirmBtn').onclick = function() {
      //   // Send AJAX request to update booking status
      //   $.ajax({
      //     url: '/admin/booking/quick-update/' + bookingId,
      //     type: 'POST',
      //     data: {
      //       action: action
      //     },
      //     success: function(response) {
      //       if (response.status === 200) {
      //         swAlert('Thông báo', 'Cập nhật trạng thái thành công!', 'success');
      //         location.reload(); // Reload page to show updated data
      //       } else {
      //         swAlert('Thông báo', 'Đã có lỗi xảy ra:' + response.message, 'error')
      //       }
      //     },
      //     error: function(xhr, status, error) {
      //       console.error('Error:', error);
      //       swAlert('Thông báo', 'Có lỗi xảy ra khi cập nhật trạng thái', 'error')
      //     },
      //     complete: function() {
      //       modal.style.display = 'none';
      //     }
      //   })
      // };
    });
  });

  // Modal close handlers
  document.getElementById('cancelBtn').addEventListener('click', function() {
    document.getElementById('approvalModal').style.display = 'none';
  });

  document.querySelector('.custom-modal__overlay').addEventListener('click', function() {
    document.getElementById('approvalModal').style.display = 'none';
  });

  // Intersection Observer for scroll animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.animation = 'slideInUp 0.6s ease-out forwards';
      }
    });
  }, observerOptions);

  // Observe table rows for scroll animation
  document.querySelectorAll('tbody tr').forEach((row, index) => {
    row.style.opacity = '0';
    row.style.transform = 'translateY(20px)';
    row.style.animationDelay = `${index * 0.1}s`;
    observer.observe(row);
  });

  // Enhanced hover effects for cards
  document.querySelectorAll('.admin-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-4px) scale(1.02)';
    });

    card.addEventListener('mouseleave', function() {
      this.style.transform = 'translateY(0) scale(1)';
    });
  });

  // Auto-refresh data every 5 minutes
  setInterval(() => {
    console.log('Refreshing dashboard data...');
    location.reload();
  }, 300000); // 5 minutes

  const additionalCSS = `
/* Styling cho trường hợp chart rỗng */
.chart-empty .donut-chart circle:last-child {
  opacity: 0.1 !important;
  stroke-dashoffset: 377 !important;
}

.chart-empty .donut-percentage {
  color: #cbd5e1 !important;
  opacity: 0.7;
}

.chart-empty .chart-info {
  opacity: 0.6;
}

.chart-empty .chart-amount {
  color: #94a3b8 !important;
}

/* Hiệu ứng khi hover vào chart rỗng */
.chart-empty:hover .donut-chart circle:last-child {
  opacity: 0.2 !important;
}

/* Animation mượt hơn cho tất cả các circles */
.donut-chart circle:last-child {
  transition: stroke-dashoffset 1.2s cubic-bezier(0.4, 0, 0.2, 1), 
              opacity 0.3s ease,
              stroke-width 0.3s ease !important;
}
`;

  // Thêm CSS vào head
  if (!document.querySelector('#chart-fix-styles')) {
    const style = document.createElement('style');
    style.id = 'chart-fix-styles';
    style.textContent = additionalCSS;
    document.head.appendChild(style);
  }
</script>
<?php end_section() ?>


<?php start_section('links') ?>
<style>
  .admin-overview-grid {
    display: grid;
    grid-template-columns: 2fr 4fr;
    gap: 2rem;
    margin-bottom: 2rem;
  }

  .admin-overview__left {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
  }

  .admin-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .admin-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  }

  .admin-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(45deg, #3b82f6, #1d4ed8);
    transform: scaleX(0);
    transition: transform 0.3s ease;
  }

  .admin-card:hover::before {
    transform: scaleX(1);
  }

  .admin-card__label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
    margin-bottom: 1rem;
  }

  .admin-card__content {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .admin-card__icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    transition: transform 0.3s ease;
  }

  .admin-card:hover .admin-card__icon {
    transform: scale(1.1);
  }

  .admin-card:nth-child(1) .admin-card__icon {
    background: #dbeafe;
    color: #3b82f6;
  }

  .admin-card:nth-child(2) .admin-card__icon {
    background: #fef3c7;
    color: #f59e0b;
  }

  .admin-card:nth-child(3) .admin-card__icon {
    background: #fde68a;
    color: #d97706;
  }

  .admin-card:nth-child(4) .admin-card__icon {
    background: #dcfce7;
    color: #16a34a;
  }

  .admin-card__value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
  }

  .admin-overview__right {
    display: flex;
    gap: 1.5rem;
  }

  .admin-chart {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    height: auto;
    display: unset;
  }

  .admin-chart:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .admin-chart__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 0px;
  }

  .admin-chart__label {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e293b;
  }

  .admin-chart__select {
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
    font-size: 0.875rem;
    color: #374151;
    cursor: pointer;
    transition: border-color 0.2s ease;
  }

  .chart-info {
    text-align: center;
    margin-top: 1rem;
  }

  .chart-amount {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
  }

  .chart-total {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0.25rem 0 0 0;
  }

  .admin-chart__select:hover {
    border-color: #3b82f6;
  }

  .chart-container {
    position: relative;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .donut-chart {
    position: relative;
    width: 150px;
    height: 150px;
  }

  .donut-chart svg {
    transform: rotate(-90deg);
    width: 100%;
    height: 100%;
  }

  /* Background circle */
  .donut-chart circle:first-child {
    stroke: #f1f5f9;
    stroke-width: 12;
    fill: none;
  }

  /* Animated progress circles */
  .donut-chart circle:last-child {
    stroke-width: 12;
    fill: none;
    stroke-linecap: round;
    transition: all 0.3s ease;
  }

  /* Revenue chart - Orange gradient */
  #revenueCircle {
    stroke: url(#revenueGradient);
    stroke-dasharray: 377;
    stroke-dashoffset: 377;
    animation: drawRevenue 2s ease-out 0.5s forwards;
  }

  /* Service chart - Green gradient */
  #serviceCircle {
    stroke: url(#serviceGradient);
    stroke-dasharray: 377;
    stroke-dashoffset: 377;
    animation: drawService 2s ease-out 1s forwards;
  }

  /* Keyframe animations */
  @keyframes drawRevenue {
    from {
      stroke-dashoffset: 377;
    }

    to {
      stroke-dashoffset: <?= 377 - (377 * $revenueStats['revenue_percentage'] / 100) ?>;
      /* 377 - (377 * 70 / 100) */
    }
  }

  @keyframes drawService {
    from {
      stroke-dashoffset: 377;
    }

    to {
      stroke-dashoffset: <?= 377 - (377 * $serviceStats['service_usage_percentage'] / 100) ?>;
      /* 377 - (377 * 60 / 100) */
    }
  }

  .donut-percentage {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    opacity: 0;
    animation: fadeInPercentage 0.5s ease-out 2s forwards;
  }

  @keyframes fadeInPercentage {
    from {
      opacity: 0;
      transform: translate(-50%, -50%) scale(0.8);
    }

    to {
      opacity: 1;
      transform: translate(-50%, -50%) scale(1);
    }
  }

  /* Hover effects for charts */
  .admin-chart:hover #revenueCircle,
  .admin-chart:hover #serviceCircle {
    stroke-width: 14;
    filter: drop-shadow(0 0 8px rgba(0, 0, 0, 0.2));
  }

  .admin-chart:hover .donut-percentage {
    transform: translate(-50%, -50%) scale(1.1);
    transition: transform 0.3s ease;
  }

  /* Table styles */
  .admin-table-wrapper {
    padding: 0 2rem;
    margin-bottom: 2rem;
  }

  .admin-table__title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 1rem;
  }

  .admin-table {
    width: 100%;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
  }

  .admin-table th {
    background: #f8fafc;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e2e8f0;
  }

  .admin-table td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: #374151;
  }

  .admin-table tr:hover {
    background: #f8fafc;
  }

  .admin-table__email {
    font-size: 0.875rem;
    color: #64748b;
  }

  .admin-table__code {
    font-size: 0.875rem;
    color: #64748b;
  }

  .status {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
  }

  .status--pending {
    background: #fef3c7;
    color: #92400e;
  }

  .status--inprogress {
    background: #dbeafe;
    color: #1e40af;
  }

  .status--success {
    background: #dcfce7;
    color: #166534;
  }



  /* Responsive design */
  @media (max-width: 1024px) {
    .admin-overview-grid {
      grid-template-columns: 1fr;
    }

    .admin-overview__left {
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }

    .admin-overview__right {
      flex-direction: row;
    }
  }

  @media (max-width: 768px) {
    .admin-overview__left {
      grid-template-columns: 1fr 1fr;
    }

    .admin-overview__right {
      flex-direction: column;
    }

    .admin-table {
      font-size: 0.875rem;
    }

    .admin-table th,
    .admin-table td {
      padding: 0.75rem 0.5rem;
    }
  }

  @media (max-width: 480px) {
    .admin-overview__left {
      grid-template-columns: 1fr;
    }

    .admin-main__header,
    .admin-overview-grid,
    .admin-table-wrapper {
      padding: 0 1rem;
    }
  }

  /* Loading animation for cards */
  .admin-card {
    opacity: 0;
    transform: translateY(20px);
    animation: slideInUp 0.6s ease-out forwards;
  }

  .admin-card:nth-child(1) {
    animation-delay: 0.1s;
  }

  .admin-card:nth-child(2) {
    animation-delay: 0.2s;
  }

  .admin-card:nth-child(3) {
    animation-delay: 0.3s;
  }

  .admin-card:nth-child(4) {
    animation-delay: 0.4s;
  }

  @keyframes slideInUp {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Chart loading animation */
  .admin-chart {
    opacity: 0;
    transform: translateY(20px);
    animation: slideInUp 0.6s ease-out 0.3s forwards;
  }

  /* Pulse effect for high values */
  .admin-card__value {
    animation: pulse 2s ease-in-out infinite;
  }

  @keyframes pulse {

    0%,
    100% {
      transform: scale(1);
    }

    50% {
      transform: scale(1.02);
    }
  }
</style>
<?php end_section() ?>
<?php
// header.php - sử dụng Auth Singleton
use App\Core\Auth;

$auth = Auth::getInstance();
$user = $auth->user();
$points = $auth->getPoints();
?>

<?php start_section('title'); ?>
<?= $title ?>
<?php end_section(); ?>

<?php start_section('links'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/account.css') ?>">
<?php end_section(); ?>


<div class="container">
  <div class="row">
    <!-- Sidebar -->
    <?php include_partial('client/sidebar-profile') ?>

    <!-- Main Content -->
    <div class="col-md-9 p-4">
      <!-- Tab Navigation -->
      <?php include_partial('client/tab-profile') ?>

      <!-- Loyalty Section -->
      <div class="loyalty-card mb-4">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h3>Loyalty</h3>
            <p class="loyalty-points mb-2" data-number="<?= $points ?>"><?= number_format($points) ?></p>
            <h5>điểm</h5>

            <div class="mt-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span>Vàng</span>
                <span>Kim cương</span>
              </div>
              <div class="progress-bar-custom">
                <div class="progress-fill"></div>
              </div>
              <div class="d-flex justify-content-between mt-1">
                <small>4,000</small>
                <small>5,000</small>
                <small>10,000</small>
              </div>
            </div>

            <button class="btn btn-light mt-3">Xem chi tiết</button>
          </div>
          <div class="col-md-6 text-end">
            <div class="badge-container justify-content-end">
              <div class="achievement-badge badge-bronze tooltip-custom" data-tooltip="Huy hiệu Đồng">
                <i class="fas fa-medal"></i>
              </div>
              <div class="achievement-badge badge-silver tooltip-custom" data-tooltip="Huy hiệu Bạc">
                <i class="fas fa-trophy"></i>
              </div>
              <div class="achievement-badge badge-gold tooltip-custom" data-tooltip="Huy hiệu Vàng">
                <i class="fas fa-crown"></i>
              </div>
              <div class="achievement-badge badge-special tooltip-custom" data-tooltip="Huy hiệu Đặc biệt">
                <i class="fas fa-star"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Thông Tin -->
        <div class="col-md-6 mb-4">
          <div class="info-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5>Thông Tin</h5>
              <a href="/user/profile/edit" class="edit-btn">Chỉnh sửa <i class="fas fa-edit"></i></a>
            </div>

            <div class="info-item">
              <div class="info-icon" style="background: var(--primary-purple);">
                <i class="fas fa-user"></i>
              </div>
              <div>
                <div class="fw-bold">Họ tên</div>
                <div class="text-muted">
                  <?= $user['name'] ?>
                </div>
              </div>
            </div>

            <div class="info-item">
              <div class="info-icon" style="background: var(--blue);">
                <i class="fas fa-envelope"></i>
              </div>
              <div>
                <div class="fw-bold">Email</div>
                <div class="text-muted"><?= $user['email'] ?? 'Chưa có thông tin' ?></div>
              </div>
            </div>

            <div class="info-item">
              <div class="info-icon" style="background: #28a745;">
                <i class="fas fa-phone"></i>
              </div>
              <div>
                <div class="fw-bold">Số điện thoại</div>
                <div class="text-muted"><?= $user['phone'] ?? 'Chưa có thông tin' ?></div>
              </div>
            </div>

            <div class="info-item">
              <div class="info-icon" style="background: #dc3545;">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <div>
                <div class="fw-bold">Địa chỉ</div>
                <div class="text-muted"><?= $user['address'] ?? 'Chưa có thông tin' ?></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tài Khoản -->
        <div class="col-md-6 mb-4">
          <div class="info-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5>Tài Khoản</h5>
              <a href="/user/profile/edit" class="edit-btn">Chỉnh sửa <i class="fas fa-edit"></i></a>
            </div>

            <div class="paypal-card">
              <i class="fab fa-paypal fa-2x"></i>
              <div class="flex-grow-1">
                <div class="fw-bold">4221 **** **** ****</div>
                <small><?= $user['name'] ?></small>
              </div>
              <div class="text-end">
                <i class="fas fa-check-circle fa-lg"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php start_section('scripts') ?>
<script>
  // Tab switching functionality
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
    });
  });

  // Sidebar navigation
  document.querySelectorAll('.sidebar-item').forEach(item => {
    item.addEventListener('click', function() {
      document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
      this.classList.add('active');
    });
  });

  // Add click effects to badges
  document.querySelectorAll('.achievement-badge').forEach(badge => {
    badge.addEventListener('click', function() {
      this.style.animation = 'none';
      setTimeout(() => {
        this.style.animation = '';
      }, 10);
    });
  });

  // Add number counting animation for loyalty points
  function animateCounter() {
    const counter = document.querySelector('.loyalty-points');
    const target = parseInt(counter.getAttribute('data-number'));
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        counter.textContent = target.toLocaleString();
        clearInterval(timer);
      } else {
        counter.textContent = Math.floor(current).toLocaleString();
      }
    }, 20);
  }

  // Start counter animation when page loads
  window.addEventListener('load', animateCounter);
</script>
<?php end_section(); ?>
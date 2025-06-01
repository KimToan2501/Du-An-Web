<?php
// header.php - sử dụng Auth Singleton
use App\Core\Auth;

$auth = Auth::getInstance();

$user = $auth->user();
?>

<div class="col-md-3 sidebar p-0">
  <div class="p-4">
    <div class="d-flex align-items-center mb-4">
      <img src="<?= show_avatar($user['avatar_url']) ?>"
        alt="Avatar" class="user-avatar me-3">
      <div>
        <h5 class="mb-0">
          <?= $user['name'] ?>
        </h5>

        <small class="text-muted">
          <?= $user['email']?>
        </small>
      </div>
    </div>

    <a href="<?= base_url('/user/profile') ?>" class="sidebar-item <?= active_link('/user/profile') ?>">
      <i class="fas fa-user"></i>
      <span>Tài khoản của tôi</span>
    </a>

    <a href="<?= base_url('/user/booking') ?>" class="sidebar-item <?= active_link('/user/booking') ?>">
      <i class="fas fa-calendar-alt"></i>
      <span>Đặt lịch</span>
    </a>
    
    <a href="<?= base_url('/user/noti') ?>" class="sidebar-item <?= active_link('/user/noti') ?>">
      <i class="fas fa-bell"></i>
      <span>Thông báo</span>
    </a>
  </div>
</div>
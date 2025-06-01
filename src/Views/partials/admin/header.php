<?php
// header.php - sử dụng Auth Singleton
use App\Core\Auth;

$auth = Auth::getInstance();
$user = $auth->user();
?>

<header class="admin-header">
    <div class="admin-header__brand">
        <!-- Start: Logo -->
        <a href="#" id="admin-header__logo-link" aria-label="Go to homepage" class="admin-header__logo-wrapper">
            <img src="<?= base_url('cms/assets/images/logo/logo.svg') ?>" alt="Logo" class="logo-image">
            <span class="admin-header__logo-text">Pawspa</span>
        </a>
        <!-- End: Logo -->
    </div>
    <div class="admin-header__toolbar">
        <!-- Start: Search -->
        <div class="admin-header__search">
            <input type="text" name="keyword" placeholder="Tìm kiếm" class="admin-header__search-input">
        </div>
        <!-- End: Search -->

        <!-- Start: Notify + Avatar -->
        <div class="admin-header__actions">
            <!-- Icon: Notify -->
            <div class="admin-header__notification-wrapper">
                <a href="#" class="admin-header__notification" id="notificationToggle">
                    <i class="fa-solid fa-bell"></i>
                    <span class="admin-header__notification-indicator"></span>
                </a>
                <!-- Notification Dropdown -->
                <div class="admin-header__notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <h4>Thông báo</h4>
                        <span class="notification-count">3</span>
                    </div>
                    <div class="notification-list">
                        <div class="notification-item unread">
                            <div class="notification-icon">
                                <i class="fa-solid fa-calendar"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-text">Có lịch hẹn mới được đặt</p>
                                <span class="notification-time">5 phút trước</span>
                            </div>
                        </div>
                        <div class="notification-item unread">
                            <div class="notification-icon">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-text">Khách hàng mới đăng ký</p>
                                <span class="notification-time">1 giờ trước</span>
                            </div>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-text">Dịch vụ đã hoàn thành</p>
                                <span class="notification-time">2 giờ trước</span>
                            </div>
                        </div>
                    </div>
                    <div class="notification-footer">
                        <a href="#" class="view-all-notifications">Xem tất cả</a>
                    </div>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="admin-header__profile-wrapper">
                <div class="admin-header__profile" id="profileToggle">
                    <img src="<?= show_avatar($user['avatar_url']) ?>" alt="Avatar"
                        class="admin-header__profile-image">
                    <p class="admin-header__profile-name"><?= htmlspecialchars($user['name']) ?></p>
                    <i class="fa-solid fa-chevron-down profile-arrow"></i>
                </div>
                <!-- Profile Dropdown -->
                <div class="admin-header__profile-dropdown" id="profileDropdown">
                    <div class="profile-dropdown-header">
                        <img src="<?= show_avatar($user['avatar_url']) ?>" alt="Avatar" class="profile-avatar">
                        <div class="profile-info">
                            <p class="profile-name"><?= htmlspecialchars($user['name']) ?></p>
                            <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                    </div>

                    <div class="profile-dropdown-menu">
                        <a href="<?= base_url('/') ?>" class="profile-menu-item">
                            <i class="fa-solid fa-square-up-right"></i>
                            <span>Website</span>
                        </a>
                        <a href="#" class="profile-menu-item">
                            <i class="fa-solid fa-user"></i>
                            <span>Hồ sơ cá nhân</span>
                        </a>
                        <a href="#" class="profile-menu-item">
                            <i class="fa-solid fa-cog"></i>
                            <span>Cài đặt</span>
                        </a>
                        <a href="#" class="profile-menu-item">
                            <i class="fa-solid fa-question-circle"></i>
                            <span>Trợ giúp</span>
                        </a>
                        <div class="profile-menu-divider"></div>
                        <a href="#" class="profile-menu-item logout logout-admin">
                            <i class="fa-solid fa-sign-out-alt"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- End: Notify + Avatar -->
    </div>
</header>
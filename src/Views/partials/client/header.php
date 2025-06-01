<?php
// header.php - sử dụng Auth Singleton
use App\Core\Auth;

$auth = Auth::getInstance();

$isLoggedIn = $auth->isLoggedIn();

$user = $auth->user();
?>


<header id="header">
    <div class="pawspa__container pawspa__flex-between">
        <!-- Start: Logo -->
        <a href="<?= base_url('/') ?>" id="pawspa-logo" aria-label="Go to homepage" class="pawspa-header__logo">
            <img src="<?= base_url('assets/images/icons/Union.svg') ?>" alt="Logo" class="pawspa-logo__image">
            <span class="pawspa-logo__text">Pawspa</span>
        </a>
        <!-- End: Logo -->

        <!-- Start: Navigation -->
        <nav id="pawspa-nav">
            <ul class="pawspa-nav__list">
                <li class="pawspa-nav__item <?= active_link('/', 'active', true) ?>">
                    <a href="<?= base_url('/') ?>">Trang chủ</a>
                </li>
                <li class="pawspa-nav__item <?= active_link('/service', 'active', true) ?>">
                    <a href="<?= base_url('/service') ?>">Dịch vụ</a>
                </li>
                <li class="pawspa-nav__item <?= active_link('/blog', 'active', true) ?>">
                    <a href="<?= base_url('/blog') ?>">Blog/Tin tức</a>
                </li>
                <li class="pawspa-nav__item <?= active_link('/introduce', 'active', true) ?>">
                    <a href="<?= base_url('/introduce') ?>">Giới thiệu</a>
                </li>
                <li class="pawspa-nav__item <?= active_link('/contact', 'active', true) ?>">
                    <a href="<?= base_url('/contact') ?>">Liên lạc</a>
                </li>
            </ul>
        </nav>
        <!-- End: Navigation -->

        <!-- Start: Icon + Action -->
        <div class="pawspa-header__actions">
            <a href="#" class="pawspa-icon__link" aria-label="Notifications">
                <img src="<?= base_url('assets/images/icons/noti.svg') ?>" alt="Notify" class="pawspa-icon__image">
            </a>
            <a href="<?= base_url('/cart') ?>" class="pawspa-icon__link" aria-label="Cart">
                <img src="<?= base_url('assets/images/icons/cart.svg') ?>" alt="Cart" class="pawspa-icon__image">
            </a>

            <?php if ($isLoggedIn && isset($user)): ?>
                <div class="pawspa-auth__links">
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
                                <?php if ($user['role'] != 'customer'): ?>
                                    <a href="<?= base_url($user['redirect_path']) ?>" class="profile-menu-item">
                                        <i class="fa-solid fa-square-up-right"></i>
                                        <span>Quản trị website</span>
                                    </a>
                                <?php endif ?>

                                <a href="/user/profile" class="profile-menu-item">
                                    <i class="fa-solid fa-user"></i>
                                    <span>Hồ sơ cá nhân</span>
                                </a>

                                <a href="/user/pets" class="profile-menu-item">
                                    <i class="fa-solid fa-dog"></i>
                                    <span>Thú cưng của tôi</span>
                                </a>

                                <a href="/user/booking" class="profile-menu-item">
                                    <i class="fa-solid fa-cog"></i>
                                    <span>Lịch sử đặt lịch</span>
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
            <?php else: ?>
                <div class="pawspa-auth__links">
                    <a href="<?= base_url('/login') ?>" class="pawspa-auth__link">Đăng nhập</a>
                    <span class="pawspa-auth__separator">/</span>
                    <a href="<?= base_url('/register') ?>" class="pawspa-auth__link">Đăng ký</a>
                </div>
            <?php endif; ?>
        </div>
        <!-- End: Icon + Action -->
    </div>
</header>
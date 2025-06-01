<aside class="admin-sidebar">
    <nav class="admin-sidebar__nav">
        <ul class="admin-sidebar__menu">
            <li class="admin-sidebar__item <?= active_link('/admin/dashboard', 'admin-sidebar__item--active') ?>">
                <a href="<?= base_url('/admin/dashboard') ?>" class="admin-sidebar__link">
                    <i class="fa-solid fa-chart-line admin-sidebar__icon"></i>
                    <span class="admin-sidebar__label">Dashboard</span>
                </a>
            </li>

            <li class="admin-sidebar__item <?= active_link('/admin/customer', 'admin-sidebar__item--active') ?>">
                <a href="<?= base_url('/admin/customer') ?>" class="admin-sidebar__link">
                    <i class="fa-solid fa-users admin-sidebar__icon"></i>
                    <span class="admin-sidebar__label">Quản lý khách hàng</span>
                </a>
            </li>

            <li class="admin-sidebar__item <?= active_link('/admin/staff', 'admin-sidebar__item--active') ?>">
                <a href="<?= base_url('/admin/staff') ?>" class="admin-sidebar__link">
                    <i class="fa-solid fa-user-nurse admin-sidebar__icon"></i>
                    <span class="admin-sidebar__label">Quản lý nhân viên</span>
                </a>
            </li>

            <li class="admin-sidebar__item <?= active_link('/admin/service-type', 'admin-sidebar__item--active', true) ?>">
                <a href="<?= base_url('/admin/service-type') ?>" class="admin-sidebar__link">
                    <i class="fa-solid fa-paw admin-sidebar__icon"></i>
                    <span class="admin-sidebar__label">Quản lý loại dịch vụ</span>
                </a>
            </li>

            <li class="admin-sidebar__item <?= active_link('/admin/service', 'admin-sidebar__item--active', true) ?>">
                <a href="<?= base_url('/admin/service') ?>" class="admin-sidebar__link">
                    <i class="fa-solid fa-dog admin-sidebar__icon"></i>
                    <span class="admin-sidebar__label">Quản lý dịch vụ</span>
                </a>
            </li>

            <li class="admin-sidebar__item <?= active_link('/admin/discount', 'admin-sidebar__item--active') ?>">
                <a href="<?= base_url('/admin/discount') ?>" class="admin-sidebar__link">
                    <i class="fa-solid fa-ticket admin-sidebar__icon"></i>
                    <span class="admin-sidebar__label">Quản lý khuyến mãi</span>
                </a>
            </li>

            <li class="admin-sidebar__item <?= active_link('/admin/booking', 'admin-sidebar__item--active') ?>">
                <a href="<?= base_url('/admin/booking') ?>" class="admin-sidebar__link">
                    <i class="fa-solid fa-calendar-check admin-sidebar__icon"></i>
                    <span class="admin-sidebar__label">Quản lý lịch hẹn</span>
                </a>
            </li>

            <li class="admin-sidebar__item <?= active_link('/admin/blog', 'admin-sidebar__item--active') ?>">
                <a href="<?= base_url('/admin/blog') ?>" class="admin-sidebar__link">
                    <i class="fa-solid fa-newspaper admin-sidebar__icon"></i>
                    <span class="admin-sidebar__label">Quản lý blog</span>
                </a>
            </li>

            <li class="admin-sidebar__item">
                <a href="#" class="admin-sidebar__link admin-sidebar__link--logout logout-admin">
                    <i class="fa-solid fa-sign-out-alt admin-sidebar__icon"></i>
                    <span class="admin-sidebar__label">Đăng xuất</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
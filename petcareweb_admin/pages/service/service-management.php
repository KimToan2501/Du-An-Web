<?php
require_once("../../../pages/connect.php");

// Số bản ghi mỗi trang
$limit = 10;

// Lấy trang hiện tại từ URL, mặc định là 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Đếm tổng số dịch vụ
$total_sql = "SELECT COUNT(*) AS total FROM services";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_services = $total_row['total'];
$total_pages = ceil($total_services / $limit);

// Lấy dữ liệu dịch vụ theo phân trang
$sql = "SELECT * FROM services LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
$services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý dịch vụ</title>

    <!-- CSS - Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Audiowide&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">

    <!-- Icon - Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/admin-common.css">
</head>
<style>
    .description-cell {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: help;
        position: relative;
    }

    /* Tooltip khi hover */
    .description-cell:hover::after {
        content: attr(title);
        position: absolute;
        left: 0;
        top: 100%;
        background: #fff;
        border: 1px solid #ddd;
        padding: 8px;
        z-index: 100;
        width: 300px;
        white-space: normal;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
</style>

<body>
    <header class="admin-header">
        <div class="admin-header__brand">
            <!-- Start: Logo -->
            <a href="#" id="admin-header__logo-link" aria-label="Go to homepage" class="admin-header__logo-wrapper">
                <img src="/petcareweb_admin/assets/images/logo/logo.svg" alt="Logo" class="logo-image">
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
                <a href="#" class="admin-header__notification">
                    <i class="fa-solid fa-bell"></i>
                    <span class="admin-header__notification-indicator"></span>
                </a>
                <div class="admin-header__profile">
                    <img src="../../assets/images/avatar/animal-avatar-bear.svg" alt="Avatar"
                        class="admin-header__profile-image">
                    <p class="admin-header__profile-name">Admin</p>
                </div>
            </div>
            <!-- End: Notify + Avatar -->
        </div>
    </header>

    <!-- Start: Sidebar -->
    <aside class="admin-sidebar">
        <nav class="admin-sidebar__nav">
            <ul class="admin-sidebar__menu">
                <li class="admin-sidebar__item">
                    <a href="/petcareweb_admin/dashboard.html" class="admin-sidebar__link">
                        <i class="fa-solid fa-chart-line admin-sidebar__icon"></i>
                        <span class="admin-sidebar__label">Dashboard</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="/petcareweb_admin/pages/customer/customer-management.html" class="admin-sidebar__link">
                        <i class="fa-solid fa-users admin-sidebar__icon"></i>
                        <span class="admin-sidebar__label">Quản lý khách hàng</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="/petcareweb_admin/pages/staff/staff-management.php" class="admin-sidebar__link">
                        <i class="fa-solid fa-user-nurse admin-sidebar__icon"></i>
                        <span class="admin-sidebar__label">Quản lý nhân viên</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="/petcareweb_admin/pages/service/service-management.php" class="admin-sidebar__link">
                        <i class="fa-solid fa-dog admin-sidebar__icon"></i>
                        <span class="admin-sidebar__label">Quản lý dịch vụ</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="/petcareweb_admin/pages/appointment/appointment-management.html"
                        class="admin-sidebar__link">
                        <i class="fa-solid fa-calendar-check admin-sidebar__icon"></i>
                        <span class="admin-sidebar__label">Quản lý lịch hẹn</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="#" class="admin-sidebar__link admin-sidebar__link--logout">
                        <i class="fa-solid fa-sign-out-alt admin-sidebar__icon"></i>
                        <span class="admin-sidebar__label">Đăng xuất</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    <!-- End: Sidebar -->

    <main class="admin-main">
        <div class="admin-main__container">
            <!-- Start: Tiêu đề & Breadcrumb -->
            <div class="admin-main__header">
                <h2 class="admin-main__title">Danh sách dịch vụ</h2>
                <!-- Start: Breadcrumb -->
                <nav class="breadcrumb">
                    <a href="/petcareweb_admin/dashboard.html" class="breadcrumb__link">Trang chủ</a>
                    <span class="breadcrumb__separator">/</span>
                    <a href="/petcareweb_admin/pages/service/service-management.php"
                        class="breadcrumb__link breadcrumb__link--active">Quản lý dịch vụ</a>
                </nav>
                <!-- End: Breadcrumb -->
            </div>
            <!-- End: Tiêu đề & Breadcrumb -->

            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success">Thêm dịch vụ thành công!</div>
            <?php endif; ?>

            <!-- Start: Khối chức năng -->
            <div class="admin-controls">
                <a href="./add-service.html" class="btn btn--primary">Thêm dịch vụ</a>

                <form class="admin-controls__search-form">
                    <input type="text" class="admin-controls__search-input" placeholder="Tìm kiếm dịch vụ">
                    <button type="submit" class="btn btn--gray">Tìm kiếm</button>
                </form>
            </div>
            <!-- End: Khối chức năng -->

            <!-- Start: Bảng dữ liệu -->
            <section class="admin-table-wrapper">
                <!-- Start: Tiêu đề bảng -->
                <h3 class="admin-table__title">Danh sách dịch vụ</h3>
                <!-- End: Tiêu đề bảng -->

                <!-- Start: Bảng dữ liệu -->
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên dịch vụ</th>
                            <th>Thời gian (phút)</th>
                            <th>Giá tiền</th>
                            <th>Mô tả</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $index => $service): ?>
                            <tr>
                                <td><?= ($offset + $index + 1) ?></td>
                                <td><?= htmlspecialchars($service['name']) ?></td>
                                <td><?= htmlspecialchars($service['duration']) ?></td>
                                <td><?= number_format($service['price'], 0, ',', '.') ?> đ</td>
                                <td class="description-cell" title="<?= htmlspecialchars($service['description']) ?>">
                                    <?=
                                        strlen($service['description']) > 50
                                        ? htmlspecialchars(substr($service['description'], 0, 50)) . '...'
                                        : htmlspecialchars($service['description'])
                                        ?>
                                </td>
                                <td>
                                    <a href="./update-service.php?id=<?= $service['service_id'] ?>">
                                        <button class="action-btn edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </a>
                                    <button class="action-btn delete" data-id="<?= $service['service_id'] ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- End: Bảng dữ liệu -->
            </section>
            <!-- End: Bảng dữ liệu -->

            <!-- Start: Phân trang -->
            <div id="pagination">
                <?php if ($page > 1): ?>
                    <a class="pagination__btn" href="?page=<?= $page - 1 ?>">
                        <i class="fa-solid fa-angle-left"></i>
                    </a>
                <?php else: ?>
                    <span class="pagination__btn pagination__btn--disabled">
                        <i class="fa-solid fa-angle-left"></i>
                    </span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a class="pagination__btn <?= $i == $page ? 'pagination__btn--active' : '' ?>" href="?page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a class="pagination__btn" href="?page=<?= $page + 1 ?>">
                        <i class="fa-solid fa-angle-right"></i>
                    </a>
                <?php else: ?>
                    <span class="pagination__btn pagination__btn--disabled">
                        <i class="fa-solid fa-angle-right"></i>
                    </span>
                <?php endif; ?>
            </div>
            <!-- End: Phân trang -->


        </div>
    </main>

    <!-- Start: Modal (popup) dùng để xác nhận các hành động -->
    <div class="custom-modal" id="approvalModal">
        <div class="custom-modal__overlay"></div>
        <div class="custom-modal__content">
            <h2 class="custom-modal__title">Tiêu đề modal</h2>

            <p class="custom-modal__message">
                <span class="modal-action-label">Bạn muốn xóa </span>
                <strong id="modalTargetName">"Tên dịch vụ"</strong>
                <span class="modal-action-suffix">?</span>
            </p>

            <div class="custom-modal__actions">
                <button class="btn btn--danger" id="cancelBtn">Hủy</button>
                <button class="btn btn--primary" id="confirmBtn">Duyệt</button>
            </div>
        </div>
    </div>
    <!-- End: Modal (popup) dùng để xác nhận các hành động -->

    <!-- Link file JS -->
    <script src="/petcareweb_admin/assets/js/sidebar.js"></script>
    <script src="/petcareweb_admin/assets/js/components/admin-modal-handler.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.action-btn.delete');

            const modal = document.getElementById('approvalModal');
            const modalTitle = modal.querySelector('.custom-modal__title');
            const modalTargetName = modal.querySelector('#modalTargetName');
            const cancelBtn = modal.querySelector('#cancelBtn');
            const confirmBtn = modal.querySelector('#confirmBtn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const serviceId = this.getAttribute('data-id');
                    const serviceName = this.closest('tr').querySelector('td:nth-child(2)').textContent;

                    modalTitle.textContent = 'Xóa dịch vụ';
                    modalTargetName.textContent = `"${serviceName}"`;

                    modal.style.display = 'block';

                    confirmBtn.onclick = function () {
                        deleteService(serviceId);
                        modal.style.display = 'none';
                    };

                    cancelBtn.onclick = function () {
                        modal.style.display = 'none';
                    };
                });
            });

            function deleteService(serviceId) {
                fetch('delete-service.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${serviceId}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Xóa dịch vụ thành công!');
                            location.reload();
                        } else {
                            alert('Lỗi khi xóa dịch vụ: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi xóa dịch vụ');
                    });
            }
        });
    </script>
</body>

</html>
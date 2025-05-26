<?php
require_once("../../../pages/connect.php");

$serviceID = $_GET['id'] ?? null;
if (!$serviceID || !is_numeric($serviceID)) {
    die("ID dịch vụ không hợp lệ");
}

// Lấy thông tin dịch vụ từ CSDL
$stmt = $conn->prepare("SELECT * FROM services WHERE service_id = ?");
$stmt->bind_param("i", $serviceID);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();

if (!$service) {
    die("Không tìm thấy dịch vụ");
}

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $duration = $_POST['duration'] ?? '';

    // Kiểm tra dữ liệu
    if (empty($name) || empty($description) || empty($price) || empty($duration)) {
        $error = "Vui lòng điền đầy đủ thông tin";
    } else {
        // Cập nhật thông tin dịch vụ
        $updateStmt = $conn->prepare("UPDATE services SET name = ?, description = ?, price = ?, duration = ? WHERE service_id = ?");
        $updateStmt->bind_param("ssssi", $name, $description, $price, $duration, $serviceID);

        if ($updateStmt->execute()) {
            header("Location: service-management.php?success=2");
            exit();
        } else {
            $error = "Cập nhật thất bại: " . $conn->error;
        }
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
                    <img src="/petcareweb_admin/assets/images/avatar/animal-avatar-bear.svg" alt="Avatar"
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
                    <a href="/petcareweb_admin/pages/staff/staff-management.html" class="admin-sidebar__link">
                        <i class="fa-solid fa-user-nurse admin-sidebar__icon"></i>
                        <span class="admin-sidebar__label">Quản lý nhân viên</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="/petcareweb_admin/pages/service/service-management.html" class="admin-sidebar__link">
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

    <!-- Start: Main content -->
    <main class="admin-main">

        <div class="admin-main__container">
            <!-- Start: Tiêu đề & Breadcrumb -->
            <div class="admin-main__header">
                <h2 class="admin-main__title">Cập nhật dịch vụ</h2>
                <!-- Start: Breadcrumb -->
                <nav class="breadcrumb">
                    <a href="/petcareweb_admin/dashboard.html" class="breadcrumb__link">Trang chủ</a>
                    <span class="breadcrumb__separator">/</span>
                    <a href="./service-management.html" class="breadcrumb__link">Quản lý dịch vụ</a>
                    <span class="breadcrumb__separator">/</span>
                    <a href="/petcareweb_admin/pages/service/service-management.html"
                        class="breadcrumb__link breadcrumb__link--active">Cập nhật dịch vụ</a>
                </nav>
                <!-- End: Breadcrumb -->
            </div>
            <!-- End: Tiêu đề & Breadcrumb -->

            <!-- Khối form thêm dịch vụ -->
            <section class="admin-form-wrapper">
                <!-- Thay đổi form như sau -->
                <form class="admin-form" method="POST" action="update-service.php?id=<?= $serviceID ?>">
                    <!-- Tên dịch vụ -->
                    <div class="admin-form__group">
                        <label for="serviceName" class="admin-form__label">Tên dịch vụ</label>
                        <input type="text" id="serviceName" name="name" class="admin-form__input"
                            placeholder="Nhập tên dịch vụ..." value="<?= htmlspecialchars($service['name']) ?>" />
                    </div>

                    <!-- Mô tả dịch vụ -->
                    <div class="admin-form__group">
                        <label for="description" class="admin-form__label">Mô tả dịch vụ</label>
                        <textarea id="description" class="admin-form__input" placeholder="Nhập mô tả dịch vụ..."
                            name="description"><?= htmlspecialchars($service['description']) ?></textarea>
                    </div>

                    <!-- Giá tiền -->
                    <div class="admin-form__group">
                        <label for="price" class="admin-form__label">Giá tiền</label>
                        <input type="text" id="price" name="price" class="admin-form__input"
                            placeholder="Nhập giá tiền..." value="<?= htmlspecialchars($service['price']) ?>" />
                    </div>

                    <!-- Thời gian thực hiện -->
                    <div class="admin-form__group">
                        <label for="duration" class="admin-form__label">Thời gian thực hiện</label>
                        <input type="text" id="duration" name="duration" class="admin-form__input"
                            placeholder="Nhập thời gian thực hiện..."
                            value="<?= htmlspecialchars($service['duration']) ?>" />
                    </div>

                    <!-- Nút lưu -->
                    <div class="admin-form__submit-wrapper">
                        <button type="submit" class="btn btn--primary admin-form__submit">Lưu</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
    <!-- End: Main content -->

    <!-- Link file JS -->
    <script src="/petcareweb_admin/assets/js/sidebar.js"></script>

</body>

</html>
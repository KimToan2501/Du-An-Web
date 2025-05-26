<?php
require_once("../../../pages/connect.php");

$customerID = $_GET['id'] ?? null;
if (!$customerID || !is_numeric($customerID)) {
    die("ID khách hàng không hợp lệ");
}

// Lấy thông tin khách hàng từ CSDL
$stmt = $conn->prepare("SELECT * FROM accounts WHERE user_id = ? AND role = 'customer'");
$stmt->bind_param("i", $customerID);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    die("Không tìm thấy khách hàng");
}

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';

    // Kiểm tra dữ liệu
    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        $error = "Vui lòng điền đầy đủ thông tin";
    } else {
        // Cập nhật thông tin khách hàng
        $updateStmt = $conn->prepare("UPDATE accounts SET name = ?, email = ?, phone = ?, address = ? WHERE user_id = ? AND role = 'customer'");
        $updateStmt->bind_param("ssssi", $name, $email, $phone, $address, $customerID);

        if ($updateStmt->execute()) {
            header("Location: customer-management.php?success=2");
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cập nhật khách hàng</title>

    <!-- CSS - Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet" />

    <!-- Icon - Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />

    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/admin-common.css" />
</head>

<body>
    <header class="admin-header">
        <div class="admin-header__brand">
            <a href="#" id="admin-header__logo-link" aria-label="Go to homepage" class="admin-header__logo-wrapper">
                <img src="/petcareweb_admin/assets/images/logo/logo.svg" alt="Logo" class="logo-image" />
                <span class="admin-header__logo-text">Pawspa</span>
            </a>
        </div>
        <div class="admin-header__toolbar">
            <div class="admin-header__search">
                <input type="text" name="keyword" placeholder="Tìm kiếm" class="admin-header__search-input" />
            </div>
            <div class="admin-header__actions">
                <a href="#" class="admin-header__notification">
                    <i class="fa-solid fa-bell"></i>
                    <span class="admin-header__notification-indicator"></span>
                </a>
                <div class="admin-header__profile">
                    <img src="/petcareweb_admin/assets/images/avatar/animal-avatar-bear.svg" alt="Avatar"
                        class="admin-header__profile-image" />
                    <p class="admin-header__profile-name">Admin</p>
                </div>
            </div>
        </div>
    </header>

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
                <!-- Các menu khác giữ nguyên -->
            </ul>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-main__container">
            <div class="admin-main__header">
                <h2 class="admin-main__title">Cập nhật khách hàng</h2>
                <nav class="breadcrumb">
                    <a href="/petcareweb_admin/dashboard.html" class="breadcrumb__link">Trang chủ</a>
                    <span class="breadcrumb__separator">/</span>
                    <a href="./customer-management.html" class="breadcrumb__link">Quản lý khách hàng</a>
                    <span class="breadcrumb__separator">/</span>
                    <a href="/petcareweb_admin/pages/customer/update-customer.php"
                        class="breadcrumb__link breadcrumb__link--active">Cập nhật khách hàng</a>
                </nav>
            </div>

            <section class="admin-form-wrapper">
                <?php if (!empty($error)) : ?>
                <div style="color: red; margin-bottom: 1rem;"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form class="admin-form" method="POST" action="update-customer.php?id=<?= $customerID ?>">
                    <div class="admin-form__group">
                        <label for="name" class="admin-form__label">Tên khách hàng</label>
                        <input type="text" id="name" name="name" class="admin-form__input"
                            placeholder="Nhập tên khách hàng..." value="<?= htmlspecialchars($customer['name']) ?>" />
                    </div>

                    <div class="admin-form__group">
                        <label for="email" class="admin-form__label">Email</label>
                        <input type="email" id="email" name="email" class="admin-form__input"
                            placeholder="Nhập email..." value="<?= htmlspecialchars($customer['email']) ?>" />
                    </div>

                    <div class="admin-form__group">
                        <label for="phone" class="admin-form__label">Số điện thoại</label>
                        <input type="text" id="phone" name="phone" class="admin-form__input"
                            placeholder="Nhập số điện thoại..." value="<?= htmlspecialchars($customer['phone']) ?>" />
                    </div>

                    <div class="admin-form__group">
                        <label for="address" class="admin-form__label">Địa chỉ</label>
                        <textarea id="address" name="address" class="admin-form__input"
                            placeholder="Nhập địa chỉ..."><?= htmlspecialchars($customer['address']) ?></textarea>
                    </div>

                    <div class="admin-form__submit-wrapper">
                        <button type="submit" class="btn btn--primary admin-form__submit">Lưu</button>
                    </div>
                </form>
            </section>
        </div>
    </main>

    <script src="/petcareweb_admin/assets/js/sidebar.js"></script>
</body>

</html>

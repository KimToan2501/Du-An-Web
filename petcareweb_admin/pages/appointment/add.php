<?php
//require_once '../../connect.php'; // sửa đúng đường dẫn connect.php tới DB
require_once("../../../pages/connect.php");

// Xử lý submit form
$success = "";
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $staff_id  = $_POST['staff_id'];
    $service_id = $_POST['service_id'];
    $booking_date = $_POST['booking_date'];

    // Kiểm tra user đã tồn tại chưa
    $user_id = null;
    $stmt = $conn->prepare("SELECT user_id FROM accounts WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $user_id = $row['user_id'];
    } else {
        $stmt2 = $conn->prepare("INSERT INTO accounts (name, email, phone, role) VALUES (?, ?, ?, 'customer')");
        $stmt2->bind_param("sss", $name, $email, $phone);
        $stmt2->execute();
        $user_id = $conn->insert_id;
        $stmt2->close();
    }
    $stmt->close();

    // Tạo order
    $order_code = uniqid("ORD");
    $stmt = $conn->prepare("INSERT INTO orders (order_code, user_id) VALUES (?, ?)");
    $stmt->bind_param("si", $order_code, $user_id);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();

    // Lấy giá dịch vụ
    $res = $conn->query("SELECT price FROM services WHERE service_id = $service_id");
    $sv = $res->fetch_assoc();
    $price = $sv['price'] ?? 0;

    // Thêm lịch hẹn
    $status = 'pending';
    $stmt = $conn->prepare("INSERT INTO bookings (order_id, user_id, booking_date, status, total_price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissd", $order_id, $user_id, $booking_date, $status, $price);
    $stmt->execute();
    $booking_id = $conn->insert_id;
    $stmt->close();

    // Thêm booking_details
    $stmt = $conn->prepare("INSERT INTO booking_details (booking_id, service_id, quantity, price) VALUES (?, ?, 1, ?)");
    $stmt->bind_param("iid", $booking_id, $service_id, $price);
    $stmt->execute();
    $stmt->close();

    $success = "Đã thêm lịch hẹn thành công!";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm lịch hẹn</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS - Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <!-- Icon - Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/admin-common.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <!-- Header & Sidebar nếu có, include vào đây -->
    <?php  include('../../header.php'); include('../../sidebar.php'); ?>

    <!-- Main content -->
    <main class="admin-main">
        <div class="admin-main__container">
            <div class="admin-main__header">
                <h2 class="admin-main__title">Thêm lịch hẹn</h2>
                <!-- Start: Breadcrumb -->
                <nav class="breadcrumb">
                    <a href="/petcareweb_admin/dashboard.html" class="breadcrumb__link">Trang chủ</a>
                    <span class="breadcrumb__separator">/</span>
                    <a href="/petcareweb_admin/pages/appointment/appointment-management.html"
                        class="breadcrumb__link">Quản lý lịch hẹn</a>
                    <span class="breadcrumb__separator">/</span>
                    <a class="breadcrumb__link--active" href="./add-customer.html">Thêm lịch hẹn</a>
                </nav>
                <!-- End: Breadcrumb -->
            </div>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <section class="admin-form-wrapper">
                <form class="admin-form" method="POST" action="">
                    <div class="admin-form__row">
                        <div class="admin-form__group">
                            <label for="name">Họ và tên khách hàng</label>
                            <input type="text" id="name" name="name" class="admin-form__input" placeholder="Nhập tên khách hàng..." required>
                        </div>
                    </div>
                    <div class="admin-form__row">
                        <div class="admin-form__group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="admin-form__input" placeholder="Nhập email khách hàng..." required>
                        </div>
                        <div class="admin-form__group">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" id="phone" name="phone" class="admin-form__input" placeholder="Nhập số điện thoại..." required>
                        </div>
                    </div>
                    <div class="admin-form__row">
                        <div class="admin-form__group">
                            <label class="admin-form__label">Nhân viên thực hiện</label>
                            <select name="staff_id" class="admin-form__input" required>
                                <option value="">-- Chọn nhân viên --</option>
                                <?php
                                $staffs = $conn->query("SELECT staff_id, name FROM staff");
                                while($s = $staffs->fetch_assoc()) {
                                    echo "<option value='{$s['staff_id']}'>{$s['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="admin-form__group">
                            <label class="admin-form__label">Dịch vụ</label>
                            <select name="service_id" class="admin-form__input" required>
                                <option value="">-- Chọn dịch vụ --</option>
                                <?php
                                $svs = $conn->query("SELECT service_id, name, price FROM services");
                                while($sv = $svs->fetch_assoc()) {
                                    echo "<option value='{$sv['service_id']}'>{$sv['name']} ({$sv['price']}đ)</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="admin-form__row">
                        <div class="admin-form__group">
                            <label for="booking_date">Ngày thực hiện</label>
                            <input type="datetime-local" id="booking_date" name="booking_date" class="admin-form__input" required>
                        </div>
                    </div>
                    <div class="admin-form__row admin-form__row--center">
                        <button type="submit" class="btn btn--primary admin-form__submit">Lưu</button>
                    </div>
                </form>
            </section>

        </div>
    </main>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
</body>
</html>

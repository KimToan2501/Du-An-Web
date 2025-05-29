<?php
require_once("../../../pages/connect.php");

// Lấy ID nhân viên từ URL
$staffId = $_GET['id'] ?? null;
if (!$staffId || !is_numeric($staffId)) {
    die("ID nhân viên không hợp lệ");
}

// Lấy thông tin nhân viên từ CSDL
$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
$stmt->bind_param("i", $staffId);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

if (!$staff) {
    die("Không tìm thấy nhân viên");
}

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    // Kiểm tra dữ liệu
    if (empty($name) || empty($email) || empty($phone)) {
        $error = "Vui lòng điền đầy đủ thông tin";
    } else {
        // Cập nhật thông tin nhân viên
        $updateStmt = $conn->prepare("UPDATE staff SET name = ?, email = ?, phone_number = ? WHERE staff_id = ?");
        $updateStmt->bind_param("sssi", $name, $email, $phone, $staffId);
        
        if ($updateStmt->execute()) {
            header("Location: staff-management.php?success=2");
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
    <title>Chỉnh sửa nhân viên</title>
    
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

    <!-- Include CSS files as in your main page -->
    <link rel="stylesheet" href="../../assets/css/admin-common.css">
</head>
<body>
<?php include('../../header.php'); include('../../sidebar.php'); ?>
    <main class="admin-main">
        <div class="admin-main__container">
            <div class="admin-main__header">
                <h2 class="admin-main__title">Chỉnh sửa nhân viên</h2>
                <nav class="breadcrumb">
                    <a href="/petcareweb_admin/dashboard.html" class="breadcrumb__link">Trang chủ</a>
                    <span class="breadcrumb__separator">/</span>
                    <a href="/petcareweb_admin/pages/staff/staff-management.php" class="breadcrumb__link">Quản lý nhân viên</a>
                    <span class="breadcrumb__separator">/</span>
                    <a class="breadcrumb__link--active" href="#">Chỉnh sửa nhân viên</a>
                </nav>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <section class="admin-form-wrapper">
                <form class="admin-form" method="post">
                    <div class="admin-form__group">
                        <label for="fullName" class="admin-form__label">Họ và tên</label>
                        <input type="text" id="fullName" name="name" class="admin-form__input" 
                               value="<?= htmlspecialchars($staff['name']) ?>" required>
                    </div>

                    <div class="admin-form__group">
                        <label for="email" class="admin-form__label">Email</label>
                        <input type="email" id="email" name="email" class="admin-form__input"
                               value="<?= htmlspecialchars($staff['email']) ?>" required>
                    </div>

                    <div class="admin-form__group">
                        <label for="phone" class="admin-form__label">Số điện thoại</label>
                        <input type="text" id="phone" name="phone" class="admin-form__input" 
                               value="<?= htmlspecialchars($staff['phone_number']) ?>" required>
                    </div>

                    <button type="submit" class="btn btn--primary admin-form__submit">Cập nhật</button>
                    <a href="staff-management.php" class="btn btn--gray">Hủy bỏ</a>
                </form>
            </section>
        </div>
    </main>


</body>
</html>
<?php
require_once("../../../pages/connect.php");

// Lấy dữ liệu từ form
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone_number = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';

// Kiểm tra dữ liệu hợp lệ
if (empty($name) || empty($email) || empty($phone_number)|| empty($address)) {
    echo "Vui lòng điền đầy đủ thông tin.";
    exit();
}

// Chuẩn bị câu truy vấn thêm khách hàng
$sql = "INSERT INTO accounts (name, email, phone, address) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "Lỗi chuẩn bị câu truy vấn: " . $conn->error;
    exit();
}

$stmt->bind_param("ssss", $name, $email, $phone_number, $address);

// Thực thi câu truy vấn
if ($stmt->execute()) {
    // Quay về trang danh sách khách hàng sau khi thêm
    header("Location: customer-management.php?success=1");
    exit();
} else {
    echo "Thêm khách hàng thất bại: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

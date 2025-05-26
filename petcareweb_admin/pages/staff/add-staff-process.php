<?php
require_once("../../../pages/connect.php");


// Lấy dữ liệu từ form
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone_number = $_POST['phone'] ?? '';


if (empty($name) || empty($email) || empty($phone_number) ) {
    echo "Vui lòng điền đầy đủ thông tin.";
    exit();
}

$sql = "INSERT INTO staff (name, email, phone_number) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $phone_number);

if ($stmt->execute()) {
    // Quay về trang danh sách nhân viên sau khi thêm
    header("Location: staff-management.php");
    exit();
} else {
    echo "Thêm nhân viên thất bại: " . $conn->error;
}

$stmt->close();
$conn->close();
?>

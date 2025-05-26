<?php
require_once("../../../pages/connect.php");


// Lấy dữ liệu từ form
$name = $_POST['service_name'] ?? '';
$duration = $_POST['duration'] ?? '';
$price = $_POST['price'] ?? '';
$description = $_POST['description'] ?? '';

if (empty($name) || empty($duration) || empty($price) || empty($description)) {
    echo "Vui lòng điền đầy đủ thông tin.";
    exit();
}

$sql = "INSERT INTO services (name, duration, price, description) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssis", $name, $duration, $price, $description);

if ($stmt->execute()) {
    // Quay về trang danh sách dịch vụ sau khi thêm
    header("Location: service-management.php");
    exit();
} else {
    echo "Thêm dịch vụ thất bại: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
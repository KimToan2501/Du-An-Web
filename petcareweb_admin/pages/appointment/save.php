<?php
require_once("../../../pages/connect.php"); // đường dẫn đúng đến file kết nối DB
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<pre>";
print_r($_POST);
echo "</pre>";
// Xử lý submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $staff_id = $_POST['staff_id'];
    $service_id = $_POST['service_id'];
    $booking_date = $_POST['booking_date'];

    // Kiểm tra user đã có chưa
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

    // Sau khi lưu xong, chuyển về trang danh sách lịch hẹn
    header("Location: management.php?success=1");
    exit();
} else {
    // Nếu truy cập trực tiếp vào save.php mà không phải submit form, có thể chuyển về form add.php
    header("Location: add.php");
    exit();
}
?>

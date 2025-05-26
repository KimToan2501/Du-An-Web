<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'petcareweb_db';

$conn = new mysqli($host, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("❌ Kết nối thất bại: " . $conn->connect_error);
} else {
    //echo "✅ Kết nối thành công!";
}
?>

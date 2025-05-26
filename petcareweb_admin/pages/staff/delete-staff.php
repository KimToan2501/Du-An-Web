<?php
require_once("../../../pages/connect.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
    exit;
}

$staffId = $_POST['id'] ?? null;
if (!$staffId || !is_numeric($staffId)) {
    echo json_encode(['success' => false, 'message' => 'ID nhân viên không hợp lệ']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE staff SET deleted_at = NOW() WHERE staff_id = ?");
    $stmt->bind_param("i", $staffId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Xóa nhân viên thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa nhân viên']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>
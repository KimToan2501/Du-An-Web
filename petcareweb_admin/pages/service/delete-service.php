<?php
require_once("../../../pages/connect.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
    exit;
}

$serviceId = $_POST['id'] ?? null;
if (!$serviceId || !is_numeric($serviceId)) {
    echo json_encode(['success' => false, 'message' => 'ID dịch vụ không hợp lệ']);
    exit;
}

try {
    $conn->begin_transaction();
    $checkStmt = $conn->prepare("SELECT service_id FROM services WHERE service_id = ?");
    $checkStmt->bind_param("i", $serviceId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Dịch vụ không tồn tại']);
        exit;
    }

    $deleteStmt = $conn->prepare("DELETE FROM services WHERE service_id = ?");
    $deleteStmt->bind_param("i", $serviceId);
    $deleteSuccess = $deleteStmt->execute();

    if ($deleteSuccess) {
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Xóa dịch vụ thành công']);
    } else {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa dịch vụ']);
    }

    $checkStmt->close();
    $deleteStmt->close();
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}

$conn->close();
?>
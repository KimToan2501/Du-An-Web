<?php
require_once("../../../pages/connect.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
    exit;
}

$customerID = $_POST['id'] ?? null;
if (!$customerID || !is_numeric($customerID)) {
    echo json_encode(['success' => false, 'message' => 'ID khách hàng không hợp lệ']);
    exit;
}

try {
    $conn->begin_transaction();
    $checkStmt = $conn->prepare("SELECT user_id FROM accounts WHERE user_id = ? AND role = 'customer'");
    $checkStmt->bind_param("i", $customerID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Khách hàng không tồn tại']);
        exit;
    }

    $deleteStmt = $conn->prepare("DELETE FROM accounts WHERE user_id = ? AND role = 'customer'");
    $deleteStmt->bind_param("i", $customerID);
    $deleteSuccess = $deleteStmt->execute();

    if ($deleteSuccess) {
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Xóa khách hàng thành công']);
    } else {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa khách hàng']);
    }

    $checkStmt->close();
    $deleteStmt->close();
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}

$conn->close();
?>
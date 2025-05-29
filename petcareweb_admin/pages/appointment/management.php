<?php
require_once("../../../pages/connect.php");
?>
<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý lịch hẹn</title>

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

    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/admin-common.css">

</head>

<body>
<?php include('../../header.php'); include('../../sidebar.php'); ?>
<main class="admin-main">
    <div class="admin-main__container">
        <div class="admin-main__header">
            <h2 class="admin-main__title">Danh sách lịch hẹn</h2>
        </div>
        <div class="admin-controls">
            <a href="add.php" class="btn btn--primary">Thêm lịch hẹn</a>
        </div>
        <section class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Dịch vụ</th>
                        <th>Khách hàng</th>
                        <th>Nhân viên</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Ngày thực hiện</th>
                        <th>Ngày tạo lịch</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
<?php
// Query lấy danh sách lịch hẹn
$sql = "SELECT b.booking_id, a.name AS customer_name, a.email, s.name AS staff_name,
    sv.name AS service_name, bd.price, b.status, b.booking_date, b.created_at
    FROM accounts a
    JOIN bookings b ON b.user_id = a.user_id
    LEFT JOIN booking_details bd ON b.booking_id = bd.booking_id
    LEFT JOIN services sv ON bd.service_id = sv.service_id
    LEFT JOIN staff s ON bd.staff_id = s.staff_id
    ORDER BY b.created_at DESC";
$res = $conn->query($sql);
$stt = 1;
while($row = $res->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$stt}</td>";
    echo "<td>{$row['service_name']}</td>";
    echo "<td>{$row['customer_name']}<br><span class='admin-table__email'>({$row['email']})</span></td>";
    echo "<td>{$row['staff_name']}</td>";
    echo "<td>{$row['price']}</td>";
    echo "<td>{$row['status']}</td>";
    echo "<td>{$row['booking_date']}</td>";
    echo "<td>{$row['created_at']}</td>";
    echo "<td>
        <a href='detail.php?id={$row['booking_id']}' class='btn btn--info'>Chi tiết</a>
        <a href='edit.php?id={$row['booking_id']}' class='btn btn--info'>Sửa</a>
        <a href='delete.php?id={$row['booking_id']}' class='btn btn--danger' onclick='return confirm(\"Bạn chắc chắn muốn xóa?\")'>Xóa</a>
    </td>";
    echo "</tr>";
    $stt++;
}
?>
                </tbody>
            </table>
        </section>
    </div>
</main>
</body>
</html>

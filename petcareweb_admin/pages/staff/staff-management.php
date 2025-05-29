<?php
require_once("../../../pages/connect.php");

// Lấy dữ liệu nhân viên từ CSDL
$sql = "SELECT * FROM staff WHERE deleted_at IS NULL";
$result = $conn->query($sql);
$staffs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $staffs[] = $row;
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="vi">
<!-- [Phần head giữ nguyên] -->
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhân viên</title>

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
            <!-- Start: Tiêu đề & Breadcrumb -->
            <div class="admin-main__header">
                <h2 class="admin-main__title">Danh sách nhân viên</h2>
                <!-- Start: Breadcrumb -->
                <nav class="breadcrumb">
                    <a href="/petcareweb_admin/dashboard.html" class="breadcrumb__link">Trang chủ</a>
                    <span class="breadcrumb__separator">/</span>
                    <a href="/petcareweb_admin/pages/staff/staff-management.html"
                        class="breadcrumb__link breadcrumb__link--active">Quản lý nhân viên</a>
                </nav>
            </div>

            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success">Thêm nhân viên thành công!</div>
            <?php endif; ?>

            <div class="admin-controls">
                <a href="./add-staff.html" class="btn btn--primary">Thêm nhân viên</a>

                <form class="admin-controls__search-form">
                    <input type="text" class="admin-controls__search-input" placeholder="Tìm kiếm nhân viên">
                    <button type="submit" class="btn btn--gray">Tìm kiếm</button>
                </form>
            </div>
            <!-- Bảng dữ liệu -->
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mã nhân viên</th>
                        <th>Họ tên nhân viên</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staffs as $index => $staff): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>NV<?= str_pad($staff['staff_id'], 3, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($staff['name']) ?></td>
                            <td><?= htmlspecialchars($staff['phone_number']) ?></td>
                            <td><?= htmlspecialchars($staff['email']) ?></td>
                            <td>
                                <a href="./update-staff.php?id=<?= $staff['staff_id'] ?>">
                                    <button class="action-btn edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </a>
                                <button class="action-btn delete" data-id="<?= $staff['staff_id'] ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <!-- Start: Modal (popup) dùng để xác nhận các hành động -->
    <div class="custom-modal" id="approvalModal">
        <div class="custom-modal__overlay"></div>
        <div class="custom-modal__content">
            <h2 class="custom-modal__title">Tiêu đề modal</h2>

            <p class="custom-modal__message">
                <span class="modal-action-label">Bạn muốn xóa </span>
                <strong id="modalTargetName">“Tên khách hàng”</strong>
                <span class="modal-action-suffix">?</span>
            </p>

            <div class="custom-modal__actions">
                <button class="btn btn--danger" id="cancelBtn">Hủy</button>
                <button class="btn btn--primary" id="confirmBtn">Duyệt</button>
            </div>
        </div>
    </div>
    <!-- End: Modal (popup) dùng để xác nhận các hành động -->

    <!-- Link file JS -->
    <!-- <script src="/petcareweb_admin/assets/js/components/pagination.js"></script> -->
    <script src="/petcareweb_admin/assets/js/sidebar.js"></script>
    <script src="/petcareweb_admin/assets/js/components/admin-modal-handler.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.action-btn.delete');
    
    const modal = document.getElementById('approvalModal');
    const modalTitle = modal.querySelector('.custom-modal__title');
    const modalTargetName = modal.querySelector('#modalTargetName');
    const cancelBtn = modal.querySelector('#cancelBtn');
    const confirmBtn = modal.querySelector('#confirmBtn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const staffId = this.getAttribute('data-id');
            const staffName = this.closest('tr').querySelector('td:nth-child(3)').textContent;
            
            modalTitle.textContent = 'Xóa nhân viên';
            modalTargetName.textContent = `"${staffName}"`;
            
            modal.style.display = 'block';
            
            confirmBtn.onclick = function() {
                deleteStaff(staffId);
                modal.style.display = 'none';
            };
            
            cancelBtn.onclick = function() {
                modal.style.display = 'none';
            };
        });
    });
    
    function deleteStaff(staffId) {
        fetch('delete-staff.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${staffId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Xóa nhân viên thành công!');
                location.reload(); 
            } else {
                alert('Lỗi khi xóa nhân viên: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa nhân viên');
        });
    }
});
</script>
</body>

</html>
<?php
require_once("../../../pages/connect.php");

// Lấy dữ liệu khách hàng từ CSDL
$sql = "SELECT * FROM accounts WHERE role='customer' ";
$result = $conn->query($sql);
$customers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khách hàng</title>

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
                <h2 class="admin-main__title">Danh sách khách hàng</h2>
                <nav class="breadcrumb">
                    <a href="/petcareweb_admin/dashboard.html" class="breadcrumb__link">Trang chủ</a>
                    <span class="breadcrumb__separator">/</span>
                    <a href="/petcareweb_admin/pages/customer/customer-management.php"
                        class="breadcrumb__link breadcrumb__link--active">Quản lý khách hàng</a>
                </nav>
            </div>

            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success">Thêm khách hàng thành công!</div>
            <?php endif; ?>

            <div class="admin-controls">
                <a href="./add-customer.html" class="btn btn--primary">Thêm khách hàng</a>
                <form class="admin-controls__search-form">
                    <input type="text" class="admin-controls__search-input" placeholder="Tìm kiếm khách hàng">
                    <button type="submit" class="btn btn--gray">Tìm kiếm</button>
                </form>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mã khách hàng</th>
                        <th>Họ tên khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $index => $customer): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>KH<?= str_pad($customer['user_id'], 3, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($customer['name']) ?></td>
                            <td><?= htmlspecialchars($customer['phone']) ?></td>
                            <td><?= htmlspecialchars($customer['email']) ?></td>
                            <td>
                                <a href="./update-customer.php?id=<?= $customer['user_id'] ?>">
                                    <button class="action-btn edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </a>
                                <button class="action-btn delete" data-id="<?= $customer['user_id'] ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div class="custom-modal" id="approvalModal">
        <div class="custom-modal__overlay"></div>
        <div class="custom-modal__content">
            <h2 class="custom-modal__title">Tiêu đề modal</h2>
            <p class="custom-modal__message">
                <span class="modal-action-label">Bạn muốn xóa </span>
                <strong id="modalTargetName">"Tên khách hàng"</strong>
                <span class="modal-action-suffix">?</span>
            </p>
            <div class="custom-modal__actions">
                <button class="btn btn--danger" id="cancelBtn">Hủy</button>
                <button class="btn btn--primary" id="confirmBtn">Duyệt</button>
            </div>
        </div>
    </div>

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
                const customerId = this.getAttribute('data-id');
                const customerName = this.closest('tr').querySelector('td:nth-child(3)').textContent;
                
                modalTitle.textContent = 'Xóa khách hàng';
                modalTargetName.textContent = `"${customerName}"`;
                
                modal.style.display = 'block';
                
                confirmBtn.onclick = function() {
                    deleteCustomer(customerId);
                    modal.style.display = 'none';
                };
                
                cancelBtn.onclick = function() {
                    modal.style.display = 'none';
                };
            });
        });
        
        function deleteCustomer(customerId) {
            fetch('delete-customer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${customerId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Xóa khách hàng thành công!');
                    location.reload(); 
                } else {
                    alert('Lỗi khi xóa khách hàng: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa khách hàng');
            });
        }
    });
    </script>
</body>
</html>
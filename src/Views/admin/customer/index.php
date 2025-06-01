<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Start: Khối chức năng -->
<div class="admin-controls">
    <a href="<?= base_url('/admin/customer/add') ?>" class="btn btn--primary">Thêm khách hàng</a>

    <form class="admin-controls__search-form">
        <input type="text" name="q" placeholder="Tìm kiếm khách hàng..." value="<?= $searching ?>" class="admin-controls__search-input">
        <button type="submit" class="btn btn--secondary">Tìm kiếm</button>
    </form>
</div>
<!-- End: Khối chức năng -->

<!-- Start: Bảng dữ liệu -->
<section class="admin-table-wrapper">
    <h3 class="admin-table__title">Danh sách khách hàng</h3>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên khách hàng</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Điểm số</th>
                <th>Vị trí</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($metadata) && count($metadata) > 0): ?>
                <?php foreach ($metadata as $item): ?>
                    <tr>
                        <td><?= $item->user_id ?></td>
                        <td><?= htmlspecialchars($item->name) ?></td>
                        <td><?= htmlspecialchars($item->email) ?></td>
                        <td><?= htmlspecialchars($item->phone ?? 'N/A') ?></td>
                        <td><?= $item->points ?> / <?= $item->getNameRanking($item->ranking) ?></td>
                        <td>Khách hàng</td>
                        <td>
                            <a href="<?= base_url('/admin/customer/update/' . $item->user_id) ?>" class="btn btn--warning">Sửa</a>
                            <button class="btn btn--danger delete-item" data-name="<?= $item->name ?>" data-id="<?= $item->user_id ?>">Xóa</button>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" align="center">Chưa có dữ liệu</td>
                </tr>
            <?php endif ?>
        </tbody>
    </table>

</section>
<!-- End: Bảng dữ liệu -->

<!-- Start: Phân trang -->
<?php include_partial('admin/pagination', ['pagination' => $pagination]) ?>
<!-- End: Phân trang -->

<?php start_section('scripts') ?>

<script>
    $('.delete-item').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: `Bạn có chắc chắn muốn xóa khách hàng <b>'${name}'</b> này?`,
            text: "Hành động này không thể hoàn tác!",
            icon: "warning",
            dangerMode: true,
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Xóa",
            cancelButtonText: "Hủy",
        }).then((willDelete) => {
            if (willDelete.isConfirmed) {
                // loadAjaxStatus("start");
                $.ajax({
                    url: `<?= base_url('/admin/customer/') ?>${id}`,
                    method: 'DELETE',
                    success: function(res) {
                        swAlert("Thông báo", res.message, "success");
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    },
                    error: function(err) {
                        swAlert("Thông báo", err.responseJSON.message, "error");
                    },
                    complete: function() {
                        // loadAjaxStatus("stop");
                    },
                });
            }
        });
    });
</script>

<?php end_section() ?>
<!-- End: Tiêu đề trang -->
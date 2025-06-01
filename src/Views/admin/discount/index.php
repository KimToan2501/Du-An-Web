<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Start: Khối chức năng -->
<div class="admin-controls">
    <a href="<?= base_url('/admin/discount/add') ?>" class="btn btn--primary">Thêm khuyến mãi</a>

    <form class="admin-controls__search-form">
        <input type="text" name="q" placeholder="Tìm kiếm khuyến mãi..." value="<?= $searching ?>" class="admin-controls__search-input">
        <button type="submit" class="btn btn--secondary">Tìm kiếm</button>
    </form>
</div>
<!-- End: Khối chức năng -->

<!-- Start: Bảng dữ liệu -->
<section class="admin-table-wrapper">
    <h3 class="admin-table__title">Danh sách khuyến mãi</h3>

    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên khuyến mãi</th>
                <th>Mã khuyến mãi</th>
                <th>Phần trăm</th>
                <th>Ngày sử dụng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($metadata) && count($metadata) > 0): ?>
                <?php $index = 1; ?>
                <?php foreach ($metadata as $item): ?>
                    <tr>
                        <td><?= $index ?></td>
                        <td><?= htmlspecialchars($item->name) ?></td>
                        <td><?= htmlspecialchars($item->code) ?></td>
                        <td><?= htmlspecialchars($item->percent) ?></td>
                        <td><?= format_date($item->start_date) ?> - <?= format_date($item->end_date) ?></td>
                        <td>
                            <a href="<?= base_url('/admin/discount/update/' . $item->discount_id) ?>">
                                <button class="action-btn edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </a>
                            <button class="action-btn delete delete-item" data-name="<?= $item->name ?>" data-id="<?= $item->discount_id ?>">
                                <i class="fa-solid fa-trash"></i>
                            </button>
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
            title: `Bạn có chắc chắn muốn xóa khuyến mãi <b>'${name}'</b> này?`,
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
                $.ajax({
                    url: `<?= base_url('/admin/discount/') ?>/${id}`,
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
                    },
                });
            }
        });
    });
</script>

<?php end_section() ?>
<!-- End: Tiêu đề trang -->
<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Start: Khối chức năng -->
<div class="admin-controls">
    <a href="<?= base_url('/admin/service-type/add') ?>" class="btn btn--primary">Thêm mới</a>

    <form class="admin-controls__search-form">
        <input type="text" class="admin-controls__search-input" placeholder="Tìm kiếm" name="q" value="<?= $searching ?? "" ?>">
        <button type="submit" class="btn btn--gray">Tìm kiếm</button>
    </form>
</div>
<!-- End: Khối chức năng -->

<!-- Start: Bảng dữ liệu -->
<section class="admin-table-wrapper">
    <!-- Start: Tiêu đề bảng -->
    <h3 class="admin-table__title">Danh sách loại dịch vụ</h3>
    <!-- End: Tiêu đề bảng -->

    <!-- Start: Bảng dữ liệu -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên loại dịch vụ</th>
                <th>Mô tả</th>
                <th>Hành động</th>
            </tr>
        </thead>

        <tbody data-type="service">
            <?php $index = 1; ?>

            <!-- Row 1 -->
            <?php if (isset($metadata) && count($metadata) > 0): ?>
                <?php foreach ($metadata as $item): ?>
                    <tr>
                        <td><?= $index ?></td>
                        <td><?= $item->name ?></td>
                        <td><?= $item->description ?? "" ?></td>
                        <td>
                            <a href="<?= base_url('/admin/service-type/update/' . $item->service_type_id) ?>">
                                <button class="action-btn edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </a>

                            <button class="action-btn delete delete-item" data-name="<?= $item->name ?>" data-id="<?= $item->service_type_id ?>">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <?php $index++ ?>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" align="center">Chưa có dữ liệu</td>
                </tr>
            <?php endif ?>
        </tbody>

    </table>
    <!-- End: Bảng dữ liệu -->

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
            title: `Bạn có chắc chắn muốn xóa loại danh mục <b>'${name}'</b> này?`,
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
                    url: `<?= base_url('/admin/service-type/') ?>/${id}`,
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
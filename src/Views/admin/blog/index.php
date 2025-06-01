<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Start: Khối chức năng -->
<div class="admin-controls">
    <a href="<?= base_url('/admin/blog/add') ?>" class="btn btn--primary">Thêm blog</a>

    <form class="admin-controls__search-form">
        <input type="text" name="q" placeholder="Tìm kiếm blog..." value="<?= $searching ?>" class="admin-controls__search-input">
        <button type="submit" class="btn btn--secondary">Tìm kiếm</button>
    </form>
</div>
<!-- End: Khối chức năng -->

<!-- Start: Bảng dữ liệu -->
<section class="admin-table-wrapper">
    <h3 class="admin-table__title">Danh sách blog</h3>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Tiêu đề</th>
                <th>Trạng thái</th>
                <th>Lượt xem</th>
                <th>Ngày tạo</th>
                <th>Chức năng</th>
            </tr>
        </thead>
        
        <tbody>
            <?php if (isset($metadata) && count($metadata) > 0): ?>
                <?php foreach ($metadata as $item): ?>
                    <tr>
                        <td><?= $item->blog_id ?></td>
                        <td>
                            <?php if (!empty($item->featured_image)): ?>
                                <img src="<?= $item->featured_image ?>" alt="Featured" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div>
                                <a href="<?= base_url('/admin/blog/details/' . $item->blog_id) ?>"><strong><?= htmlspecialchars($item->title) ?></strong></a>
                                <br>
                                <small class="text-muted">/<?= htmlspecialchars($item->slug) ?></small>
                            </div>
                        </td>
                        <td>
                            <?php
                            $statusClass = [
                                'draft' => 'warning',
                                'published' => 'success',
                                'archived' => 'secondary'
                            ];
                            $statusText = [
                                'draft' => 'Nháp',
                                'published' => 'Đã xuất bản',
                                'archived' => 'Lưu trữ'
                            ];
                            ?>
                            <span class="badge bg-<?= $statusClass[$item->status] ?? 'secondary' ?>">
                                <?= $statusText[$item->status] ?? $item->status ?>
                            </span>
                        </td>
                        <td>
                            <i class="fas fa-eye text-muted"></i> <?= number_format($item->view_count) ?>
                        </td>
                        <td>
                            <?= date('d/m/Y H:i', strtotime($item->created_at)) ?>
                        </td>
                        <td>
                            <a href="<?= base_url('/admin/blog/update/' . $item->blog_id) ?>">
                                <button class="action-btn edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </a>
                            <button class="action-btn delete delete-item" data-id="<?= $item->blog_id ?>" data-name="<?= $item->title ?>" title="Xóa">
                                <i class="fa-solid fa-trash"></i>
                            </button>

                        </td>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" align="center">Chưa có dữ liệu</td>
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
            title: `Bạn có chắc chắn muốn xóa blog <b>'${name}'</b> này?`,
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
                loadAjaxStatus("start");
                $.ajax({
                    url: `<?= base_url('/admin/blog/') ?>${id}`,
                    method: 'DELETE',
                    success: function(res) {
                        swAlert("Thông báo", res.message, "success");
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    },
                    error: function(err) {
                        console.log(err.responseJSON);
                        swAlert("Thông báo", err.responseJSON.message, "error");
                    },
                    complete: function() {
                        loadAjaxStatus("stop");
                    },
                });
            }
        });
    });
</script>

<?php end_section() ?>
<!-- End: Tiêu đề trang -->
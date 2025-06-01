<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Khối chi tiết blog -->
<section class="admin-detail-wrapper">
    <div class="admin-detail">
        <!-- Header với actions -->
        <div class="admin-detail__header">
            <div class="admin-detail__title">
                <h2><?= htmlspecialchars($metadata->title) ?></h2>
                <div class="admin-detail__meta">
                    <span class="badge badge--<?= $metadata->status === 'published' ? 'success' : ($metadata->status === 'draft' ? 'warning' : 'secondary') ?>">
                        <?= $metadata->status === 'published' ? 'Đã xuất bản' : ($metadata->status === 'draft' ? 'Bản nháp' : 'Lưu trữ') ?>
                    </span>
                    <span class="meta-info">Slug: <strong><?= $metadata->slug ?></strong></span>
                </div>
            </div>
            
            <div class="admin-detail__actions">
                <a href="<?= base_url('/admin/blog/update/' . $metadata->blog_id) ?>" class="btn btn--primary btn--sm">
                    <i class="icon-edit"></i> Chỉnh sửa
                </a>
                <button type="button" class="btn btn--danger btn--sm" onclick="deleteBlog(<?= $metadata->blog_id ?>)">
                    <i class="icon-trash"></i> Xóa
                </button>
                <a href="<?= base_url('/admin/blog') ?>" class="btn btn--secondary btn--sm">
                    <i class="icon-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <!-- Thông tin cơ bản -->
        <div class="admin-detail__section">
            <h3 class="admin-detail__section-title">Thông tin cơ bản</h3>
            <div class="admin-detail__grid">
                <div class="admin-detail__item">
                    <label class="admin-detail__label">Ngày tạo:</label>
                    <span class="admin-detail__value"><?= date('d/m/Y H:i', strtotime($metadata->created_at)) ?></span>
                </div>
                
                <div class="admin-detail__item">
                    <label class="admin-detail__label">Ngày cập nhật:</label>
                    <span class="admin-detail__value"><?= date('d/m/Y H:i', strtotime($metadata->updated_at)) ?></span>
                </div>
                
                <div class="admin-detail__item">
                    <label class="admin-detail__label">Trạng thái:</label>
                    <span class="admin-detail__value">
                        <span class="badge badge--<?= $metadata->status === 'published' ? 'success' : ($metadata->status === 'draft' ? 'warning' : 'secondary') ?>">
                            <?= $metadata->status === 'published' ? 'Đã xuất bản' : ($metadata->status === 'draft' ? 'Bản nháp' : 'Lưu trữ') ?>
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Ảnh đại diện -->
        <?php if (!empty($metadata->featured_image)): ?>
        <div class="admin-detail__section">
            <h3 class="admin-detail__section-title">Ảnh đại diện</h3>
            <div class="admin-detail__image">
                <img src="<?= base_url($metadata->featured_image) ?>" alt="<?= htmlspecialchars($metadata->title) ?>" class="featured-image">
            </div>
        </div>
        <?php endif; ?>

        <!-- Tóm tắt -->
        <?php if (!empty($metadata->excerpt)): ?>
        <div class="admin-detail__section">
            <h3 class="admin-detail__section-title">Tóm tắt</h3>
            <div class="admin-detail__content">
                <?= nl2br(htmlspecialchars($metadata->excerpt)) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Nội dung -->
        <div class="admin-detail__section">
            <h3 class="admin-detail__section-title">Nội dung</h3>
            <div class="admin-detail__content">
                <?= $metadata->content ?>
            </div>
        </div>

        <!-- SEO Meta -->
        <?php if (!empty($metadata->meta_title) || !empty($metadata->meta_description)): ?>
        <div class="admin-detail__section">
            <h3 class="admin-detail__section-title">Thông tin SEO</h3>
            <div class="admin-detail__grid">
                <?php if (!empty($metadata->meta_title)): ?>
                <div class="admin-detail__item">
                    <label class="admin-detail__label">Meta Title:</label>
                    <span class="admin-detail__value"><?= htmlspecialchars($metadata->meta_title) ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($metadata->meta_description)): ?>
                <div class="admin-detail__item">
                    <label class="admin-detail__label">Meta Description:</label>
                    <span class="admin-detail__value"><?= htmlspecialchars($metadata->meta_description) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php start_section('styles') ?>
<style>
.admin-detail-wrapper {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.admin-detail {
    padding: 24px;
}

.admin-detail__header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 32px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e5e5e5;
}

.admin-detail__title h2 {
    margin: 0 0 8px 0;
    font-size: 24px;
    font-weight: 600;
    color: #333;
}

.admin-detail__meta {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.admin-detail__actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.admin-detail__section {
    margin-bottom: 32px;
}

.admin-detail__section:last-child {
    margin-bottom: 0;
}

.admin-detail__section-title {
    margin: 0 0 16px 0;
    font-size: 18px;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 8px;
}

.admin-detail__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
}

.admin-detail__item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.admin-detail__label {
    font-weight: 600;
    color: #666;
    font-size: 14px;
}

.admin-detail__value {
    font-size: 14px;
    color: #333;
}

.admin-detail__content {
    line-height: 1.6;
    color: #333;
}

.admin-detail__image {
    text-align: center;
}

.featured-image {
    max-width: 100%;
    max-height: 400px;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
}

.badge--success {
    background: #d4edda;
    color: #155724;
}

.badge--warning {
    background: #fff3cd;
    color: #856404;
}

.badge--secondary {
    background: #e2e3e5;
    color: #383d41;
}

.meta-info {
    font-size: 14px;
    color: #666;
}

.btn--sm {
    padding: 6px 12px;
    font-size: 14px;
}

@media (max-width: 768px) {
    .admin-detail__header {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }
    
    .admin-detail__actions {
        justify-content: flex-start;
    }
    
    .admin-detail__grid {
        grid-template-columns: 1fr;
    }
}
</style>
<?php end_section() ?>

<?php start_section('scripts') ?>
<script>
function deleteBlog(blogId) {
    swAlert(
        'Xác nhận xóa',
        'Bạn có chắc chắn muốn xóa blog này? Hành động này không thể hoàn tác.',
        'warning',
        {
            showCancelButton: true,
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy',
            confirmButtonColor: '#dc3545'
        }
    ).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `<?= base_url('/admin/blog/delete/') ?>${blogId}`,
                type: 'DELETE',
                success: function(response) {
                    swAlert('Thông báo', response.message, 'success');
                    if (response.status) {
                        setTimeout(function() {
                            window.location.href = '<?= base_url('/admin/blog') ?>';
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    swAlert('Thông báo', xhr.responseJSON?.message || 'Có lỗi xảy ra', 'error');
                }
            });
        }
    });
}
</script>
<?php end_section() ?>
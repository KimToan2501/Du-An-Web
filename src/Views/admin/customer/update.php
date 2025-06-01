<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Khối form thêm khách hàng -->
<section class="admin-form-wrapper">
    <form id="form-add" class="admin-form">
        <!-- Tên khách hàng -->
        <div class="admin-form__group">
            <label for="customerName" class="admin-form__label">Tên khách hàng (*)</label>
            <input type="text" id="customerName" name="name" class="admin-form__input" required value="<?= $metadata->name ?>">
        </div>

        <!-- Email -->
        <div class="admin-form__group">
            <label for="customerEmail" class="admin-form__label">Email (*)</label>
            <input type="email" id="customerEmail" name="email" class="admin-form__input" required value="<?= $metadata->email ?>">
        </div>

        <!-- Số điện thoại -->
        <div class="admin-form__group">
            <label for="customerPhone" class="admin-form__label">Số điện thoại</label>
            <input type="text" id="customerPhone" name="phone" class="admin-form__input" value="<?= $metadata->phone ?>">
        </div>

        <!-- Địa chỉ -->
        <div class="admin-form__group">
            <label for="customerAddress" class="admin-form__label">Địa chỉ</label>
            <textarea id="customerAddress" name="address" class="admin-form__textarea"><?= $metadata->address ?></textarea>
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="btn btn--primary">Lưu thay đổi</button>
            <a href="<?= base_url('/admin/customer') ?>" class="btn btn--secondary">Hủy</a>
        </div>
    </form>
</section>

<?php start_section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#form-add').submit(function(e) {
            e.preventDefault();

            const formData = {
                name: $('#customerName').val(),
                email: $('#customerEmail').val(),
                phone: $('#customerPhone').val(),
                address: $('#customerAddress').val()
            };

            $.ajax({
                url: '<?= base_url('/admin/customer/update/' . $metadata->user_id) ?>',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    swAlert('Thông báo', response.message, 'success');
                    if (response.status) {
                        setTimeout(function() {
                            window.location.href = '<?= base_url('/admin/customer') ?>';
                        }, 1000); // Redirect after 1 second
                    }
                },
                error: function(xhr) {
                    swAlert('Thông báo', xhr.responseJSON?.message || 'Có lỗi xảy ra', 'error');
                }
            });
        });
    });
</script>
<?php end_section() ?>
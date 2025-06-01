<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Khối form thêm nhân viên -->
<section class="admin-form-wrapper">
    <form id="form-add" class="admin-form">
        <!-- Tên nhân viên -->
        <div class="admin-form__group">
            <label for="staffName" class="admin-form__label">Tên nhân viên (*)</label>
            <input type="text" id="staffName" name="name" class="admin-form__input" required value="<?= $metadata->name ?>">
        </div>

        <!-- Email -->
        <div class="admin-form__group">
            <label for="staffEmail" class="admin-form__label">Email (*)</label>
            <input type="email" id="staffEmail" name="email" class="admin-form__input" required value="<?= $metadata->email ?>">
        </div>

        <!-- Số điện thoại -->
        <div class="admin-form__group">
            <label for="staffPhone" class="admin-form__label">Số điện thoại</label>
            <input type="text" id="staffPhone" name="phone" class="admin-form__input" value="<?= $metadata->phone ?>">
        </div>

        <!-- Địa chỉ -->
        <div class="admin-form__group">
            <label for="staffAddress" class="admin-form__label">Địa chỉ</label>
            <textarea id="staffAddress" name="address" class="admin-form__textarea"><?= $metadata->address ?></textarea>
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="btn btn--primary">Lưu thay đổi</button>
            <a href="<?= base_url('/admin/staff') ?>" class="btn btn--secondary">Hủy</a>
        </div>
    </form>
</section>

<?php start_section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#form-add').submit(function(e) {
            e.preventDefault();

            const formData = {
                name: $('#staffName').val(),
                email: $('#staffEmail').val(),
                phone: $('#staffPhone').val(),
                address: $('#staffAddress').val()
            };

            $.ajax({
                url: '<?= base_url('/admin/staff/update/' . $metadata->user_id) ?>',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    swAlert('Thông báo', response.message, 'success');
                    if (response.status) {
                        setTimeout(function() {
                            window.location.href = '<?= base_url('/admin/staff') ?>';
                        }, 2000); // Redirect after 1 second
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
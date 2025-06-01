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
            <input type="text" id="staffName" name="name" class="admin-form__input" required>
        </div>

        <!-- Email -->
        <div class="admin-form__group">
            <label for="staffEmail" class="admin-form__label">Email (*)</label>
            <input type="email" id="staffEmail" name="email" class="admin-form__input" required>
        </div>

        <!-- Mật khẩu -->
        <div class="admin-form__group">
            <label for="staffPassword" class="admin-form__label">Mật khẩu (*)</label>
            <input type="password" id="staffPassword" name="password" class="admin-form__input" required>
        </div>

        <!-- Số điện thoại -->
        <div class="admin-form__group">
            <label for="staffPhone" class="admin-form__label">Số điện thoại</label>
            <input type="text" id="staffPhone" name="phone" class="admin-form__input">
        </div>

        <!-- Địa chỉ -->
        <div class="admin-form__group">
            <label for="staffAddress" class="admin-form__label">Địa chỉ</label>
            <textarea id="staffAddress" name="address" class="admin-form__textarea"></textarea>
        </div>

        <!-- Ảnh đại diện -->
        <div class="admin-form__group">
            <label for="staffAvatar" class="admin-form__label">Ảnh đại diện (*)</label>
            <input type="file" id="staffAvatar" name="avatar" class="admin-form__input" accept="image/*" required>
            <div class="image-preview mt-1" style="display:none;">
                <img id="avatarPreview" src="#" alt="Preview" class="avatar-preview">
            </div>
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="btn btn--primary">Thêm nhân viên</button>
            <a href="<?= base_url('/admin/staff') ?>" class="btn btn--secondary">Hủy</a>
        </div>
    </form>
</section>

<?php start_section('scripts') ?>
<script>
    $(document).ready(function() {
        // Image preview
        $('#staffAvatar').change(function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#avatarPreview').attr('src', e.target.result);
                    $('.image-preview').show();
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        $('#form-add').submit(function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('name', $('#staffName').val());
            formData.append('email', $('#staffEmail').val());
            formData.append('password', $('#staffPassword').val());
            formData.append('phone', $('#staffPhone').val());
            formData.append('address', $('#staffAddress').val());
            formData.append('avatar', $('#staffAvatar')[0].files[0]);

            $.ajax({
                url: '<?= base_url('/admin/staff/add') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    swAlert('Thông báo', response.message, 'success');
                    if (response.status) {
                        setTimeout(function() {
                            window.location.href = '<?= base_url('/admin/staff') ?>';
                        }, 1000);
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
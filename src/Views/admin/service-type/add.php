<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Khối form thêm dịch vụ -->
<section class="admin-form-wrapper">
    <form class="admin-form" id="form-add">
        <!-- Tên dịch vụ -->
        <div class="admin-form__group">
            <label for="serviceName" class="admin-form__label">Tên loại dịch vụ</label>
            <input type="text" name="name" id="serviceName" class="admin-form__input"
                placeholder="Nhập tên loại dịch vụ..." />
        </div>

        <!-- Nhóm dịch vụ -->
        <div class="admin-form__group">
            <label for="serviceGroup" class="admin-form__label">Nhóm dịch vụ</label>
            <textarea type="text" id="serviceGroup" name="description" class="admin-form__input"
                placeholder="Mô tả..." ></textarea>
        </div>

        <!-- Nút lưu -->
        <div class="admin-form__submit-wrapper">
            <button type="submit" class="btn btn--primary admin-form__submit">Lưu</button>
        </div>
    </form>
</section>


<?php start_section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#form-add').submit(function(e) {
            e.preventDefault();

            const name = $(this).find('input[name="name"]').val();
            const description = $(this).find('textarea[name="description"]').val();

            if (!name) {
                swAlert('Thông báo', 'Vui lòng nhập đầy đủ thông tin', 'warning')
                return;
            }

            const data = {
                name: name,
                description: description
            }

            $.ajax({
                url: '<?= base_url('/admin/service-type/add') ?>',
                dataType: 'json',
                contentType: 'application/json',
                type: 'POST',
                data: JSON.stringify(data),
                success: function(response) {
                    swAlert('Thông báo', response.message, 'success')

                    setTimeout(() => {
                        window.location.href = "<?= base_url('/admin/service-type') ?>"
                    }, 1000)
                },
                error: function(xhr, status, error) {
                    console.log(`error`, xhr.response)

                    swAlert('Thông báo', xhr.responseJSON.message, 'error')
                }
            })
        })
    })
</script>
<?php end_section() ?>
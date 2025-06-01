<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<div class="admin-form-wrapper">
    <div class="content">
        <form id="form-handler" class="admin-form">
            <div class="admin-form__group">
                <label class="admin-form__label" for="name">Tên khuyến mãi</label>
                <input type="text" id="name" name="name" required class="admin-form__input" placeholder="Nhập tên khuyến mãi" value="<?= $metadata->name ?>">
            </div>

            <div class="admin-form__group">
                <label class="admin-form__label" for="code">Mã khuyến mãi</label>
                <div>
                    <input type="text" id="code" name="code" disabled required class="admin-form__input" placeholder="Nhập mã hoặc tạo tự động" value="<?= $metadata->code ?>">
                    <!-- <button type="button" id="generateCodeBtn" class="btn btn--gray mt-1">Tạo mã</button> -->
                </div>
            </div>

            <div class="admin-form__group">
                <label class="admin-form__label" for="percent">Phần trăm (%)</label>
                <input type="number" id="percent" name="percent" min="0" max="100" required class="admin-form__input" placeholder="Nhập phần trăm khuyến mãi" value="<?= $metadata->percent ?>">
            </div>

            <div class="admin-form__group">
                <label class="admin-form__label" for="start_date">Ngày bắt đầu</label>
                <input type="date" id="start_date" name="start_date" required class="admin-form__input" placeholder="Chọn ngày bắt đầu" value="<?= format_date($metadata->start_date, 'Y-m-d') ?>">
            </div>

            <div class="admin-form__group">
                <label class="admin-form__label" for="end_date">Ngày kết thúc</label>
                <input type="date" id="end_date" name="end_date" required class="admin-form__input" placeholder="Chọn ngày kết thúc" value="<?= format_date($metadata->end_date, 'Y-m-d') ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary">Lưu thay đổi</button>
                <a href="/admin/discount" class="btn btn--gray">Huỷ bỏ</a>
            </div>
        </form>
    </div>
</div>

<?php start_section('scripts') ?>
<script>
    $(document).ready(function() {
        // Set min date for start_date and end_date to today
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
        const dd = String(today.getDate()).padStart(2, '0');
        const todayFormatted = `${yyyy}-${mm}-${dd}`;

        $('#start_date').attr('min', todayFormatted);
        $('#end_date').attr('min', todayFormatted);

        $('#form-handler').on('submit', function(e) {
            e.preventDefault();

            const name = $('#name').val();
            const percent = $('#percent').val();
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (!name || !percent || !startDate || !endDate) {
                swAlert('Thông báo', 'Vui lòng điền đầy đủ thông tin', 'error');
                return;
            }

            if (percent < 0 || percent > 100) {
                swAlert('Thông báo', 'Phần trăm khuyến mãi phải từ 0 đến 100', 'error');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                swAlert('Thông báo', 'Ngày bắt đầu không thể lớn hơn ngày kết thúc', 'error');
                return;
            }

            const data = {
                name: name,
                percent: percent,
                start_date: startDate,
                end_date: endDate
            }

            $.ajax({
                url: '<?= base_url('/admin/discount/update/' . $metadata->discount_id) ?>',
                method: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    swAlert('Thông báo', response.message, 'success');
                    setTimeout(function() {
                        window.location.href = '/admin/discount';
                    }, 1500);
                },
                error: function(error) {
                    swAlert('Thông báo', error.responseJSON?.message || 'Đã xảy ra lỗi', 'error');
                }
            });
        });
    });
</script>
<?php end_section() ?>
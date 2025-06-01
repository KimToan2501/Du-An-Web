<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Khối form thêm dịch vụ -->
<section class="admin-form-wrapper">
    <form id="form-add" class="admin-form">
        <!-- Tên dịch vụ -->
        <div class="admin-form__group">
            <label for="serviceName" class="admin-form__label">Tên dịch vụ (*)</label>
            <input type="text" name="name" id="serviceName" class="admin-form__input"
                placeholder="Nhập tên dịch vụ..." />
        </div>

        <div class="admin-form__group">
            <label for="description" class="admin-form__label">Mô tả dịch vụ</label>
            <textarea type="text" name="description" id="description" class="admin-form__input"
                placeholder="Nhập mô tả dịch vụ..."></textarea>
        </div>

        <!-- Nhóm dịch vụ -->
        <div class="admin-form__group">
            <label for="serviceGroup" class="admin-form__label">Nhóm dịch vụ (*)</label>
            <select name="service_type_id" class="admin-form__input" id="">
                <option value="" disabled selected>-- Chọn loại dịch vụ --</option>
                <?php foreach ($serviceTypes as $item): ?>
                    <option value="<?= $item->service_type_id ?>"><?= $item->name ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <!-- Giá tiền -->
        <div class="admin-form__group">
            <label for="price" class="admin-form__label">Giá tiền (*)</label>
            <input type="number" id="price" name="price" class="admin-form__input" placeholder="Nhập giá tiền..." />
        </div>

        <!-- Thời gian -->
        <div class="admin-form__group">
            <label for="duration" class="admin-form__label">Tổng thời gian làm (*)</label>
            <input type="number" id="duration" name="duration" class="admin-form__input" placeholder="Tổng thời gian làm (phút)..." />
        </div>

        <!-- Giảm giá -->
        <div class="admin-form__group">
            <label for="discount_percent" class="admin-form__label">Phần trăm giảm giá</label>
            <input type="number" id="discount_percent" name="discount_percent" class="admin-form__input" placeholder="Nhập phần trăm giảm giá..." />
        </div>

        <!-- Upload ảnh -->
        <div class="admin-form__group">
            <label for="service_images" class="admin-form__label">Hình ảnh dịch vụ</label>
            <div class="image-upload-container">
                <input type="file" id="service_images" name="service_images[]" multiple accept="image/*" class="admin-form__input image-input" />
                <div class="image-upload-preview" id="image-preview">
                    <!-- Preview images will be displayed here -->
                </div>
            </div>
        </div>

        <!-- Nút lưu -->
        <div class="admin-form__submit-wrapper">
            <button type="submit" class="btn btn--primary admin-form__submit">Lưu</button>
        </div>
    </form>
</section>



<?php start_section('scripts') ?>
<script>
    let selectedFiles = [];

    $(document).ready(function() {
        // Handle file selection
        $('#service_images').on('change', function(e) {
            const files = Array.from(e.target.files);

            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    selectedFiles.push(file);
                    displayImagePreview(file, selectedFiles.length - 1);
                }
            });

            // Clear the input so same file can be selected again
            $(this).val('');
        });

        // Display image preview
        function displayImagePreview(file, index) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const previewHtml = `
                    <div class="image-preview-item" data-index="${index}">
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="image-remove-btn" onclick="removeImage(${index})">&times;</button>
                    </div>
                `;

                $('#image-preview').append(previewHtml);
            };

            reader.readAsDataURL(file);
        }

        // Form submission
        $('#form-add').submit(function(e) {
            e.preventDefault();

            const name = $(this).find('input[name="name"]').val();
            const serviceTypeId = $(this).find('select[name="service_type_id"]').val();
            const price = $(this).find('input[name="price"]').val();
            const discountPercent = $(this).find('input[name="discount_percent"]').val();
            const duration = $(this).find('input[name="duration"]').val();
            const description = $(this).find('textarea[name="description"]').val();

            if (!name || !serviceTypeId || !price || !duration) {
                swAlert('Thông báo', 'Vui lòng nhập đầy đủ thông tin bắt buộc', 'warning')
                return;
            }

            if (discountPercent) {
                if (discountPercent < 0 || discountPercent > 100) {
                    swAlert('Thông báo', 'Phần trăm giảm giá từ 0 - 100%', 'warning');
                    return;
                }
            }

            // Create FormData for file upload
            const formData = new FormData();
            formData.append('name', name);
            formData.append('service_type_id', serviceTypeId);
            formData.append('price', price);
            formData.append('discount_percent', discountPercent || 0);
            formData.append('duration', duration);
            formData.append('description', description);

            // Add images to FormData
            selectedFiles.forEach((file, index) => {
                formData.append('service_images[]', file);
            });

            $.ajax({
                url: '<?= base_url('/admin/service/add') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    swAlert('Thông báo', result.message, 'success')

                    setTimeout(() => {
                        window.location.href = "<?= base_url('/admin/service') ?>"
                    }, 1000)
                },
                error: function(xhr, status, error) {
                    console.log('error', xhr.responseText)

                    let errorMessage = 'Có lỗi xảy ra khi thêm dịch vụ';
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        errorMessage = errorResponse.message || errorMessage;
                    } catch (e) {
                        // Use default error message
                    }

                    swAlert('Thông báo', errorMessage, 'error')
                }
            })
        })
    });

    // Remove image function
    function removeImage(index) {
        // Remove from selectedFiles array
        selectedFiles.splice(index, 1);

        // Remove preview element
        $(`.image-preview-item[data-index="${index}"]`).remove();

        // Update indices for remaining items
        $('.image-preview-item').each(function(newIndex) {
            $(this).attr('data-index', newIndex);
            $(this).find('.image-remove-btn').attr('onclick', `removeImage(${newIndex})`);
        });
    }
</script>
<?php end_section() ?>
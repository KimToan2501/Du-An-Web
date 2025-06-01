<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Khối form cập nhật dịch vụ -->
<section class="admin-form-wrapper">
    <form id="form-update" class="admin-form">
        <!-- Tên dịch vụ -->
        <div class="admin-form__group">
            <label for="serviceName" class="admin-form__label">Tên dịch vụ (*)</label>
            <input type="text" name="name" id="serviceName" class="admin-form__input"
                placeholder="Nhập tên dịch vụ..." value="<?= $metadata->name ?>" />
        </div>

        <div class="admin-form__group">
            <label for="description" class="admin-form__label">Mô tả dịch vụ</label>
            <textarea type="text" name="description" id="description" class="admin-form__input"
                placeholder="Nhập mô tả dịch vụ..."><?= $metadata->description ?></textarea>
        </div>

        <!-- Nhóm dịch vụ -->
        <div class="admin-form__group">
            <label for="serviceGroup" class="admin-form__label">Nhóm dịch vụ (*)</label>
            <select name="service_type_id" class="admin-form__input" id="">
                <option value="" disabled>-- Chọn loại dịch vụ --</option>
                <?php foreach ($serviceTypes as $item): ?>
                    <option value="<?= $item->service_type_id ?>" <?= $metadata->service_type_id == $item->service_type_id ? 'selected' : '' ?>><?= $item->name ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <!-- Giá tiền -->
        <div class="admin-form__group">
            <label for="price" class="admin-form__label">Giá tiền (*)</label>
            <input type="number" id="price" name="price" class="admin-form__input" value="<?= to_int($metadata->price) ?>" placeholder="Nhập giá tiền..." />
        </div>

        <!-- Thời gian -->
        <div class="admin-form__group">
            <label for="duration" class="admin-form__label">Tổng thời gian làm (*)</label>
            <input type="number" id="duration" name="duration" class="admin-form__input" value="<?= $metadata->duration ?>" placeholder="Tổng thời gian làm (phút)..." />
        </div>

        <!-- Giảm giá -->
        <div class="admin-form__group">
            <label for="discount_percent" class="admin-form__label">Phần trăm giảm giá</label>
            <input type="number" id="discount_percent" name="discount_percent" value="<?= $metadata->discount_percent ?>" class="admin-form__input" placeholder="Nhập phần trăm giảm giá..." />
        </div>

        <!-- Ảnh hiện có -->
        <?php if (isset($serviceImages) && !empty($serviceImages)): ?>
        <div class="admin-form__group">
            <label class="admin-form__label">Ảnh hiện có</label>
            <div class="existing-images-container">
                <?php foreach ($serviceImages as $image): ?>
                <div class="existing-image-item" data-image-id="<?= $image->image_id ?>">
                    <img src="<?= base_url($image->image_url) ?>" alt="Service Image">
                    <button type="button" class="image-remove-btn existing-image-remove" onclick="removeExistingImage(<?= $image->image_id ?>)">&times;</button>
                </div>
                <?php endforeach ?>
            </div>
        </div>
        <?php endif ?>

        <!-- Upload ảnh mới -->
        <div class="admin-form__group">
            <label for="service_images" class="admin-form__label">Thêm ảnh mới</label>
            <div class="image-upload-container">
                <input type="file" id="service_images" name="service_images[]" multiple accept="image/*" class="admin-form__input image-input" />
                <div class="image-upload-preview" id="image-preview">
                    <!-- Preview images will be displayed here -->
                </div>
            </div>
        </div>

        <!-- Nút lưu -->
        <div class="admin-form__submit-wrapper">
            <button type="submit" class="btn btn--primary admin-form__submit">Cập nhật</button>
        </div>
    </form>
</section>

<style>
.image-upload-container {
    margin-top: 10px;
}

.image-upload-preview, .existing-images-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 15px;
}

.image-preview-item, .existing-image-item {
    position: relative;
    width: 150px;
    height: 150px;
    border: 2px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background-color: #f8f9fa;
}

.existing-image-item {
    border-color: #28a745;
}

.image-preview-item img, .existing-image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
    z-index: 10;
}

.image-remove-btn:hover {
    background-color: #c82333;
}

.existing-image-remove {
    background-color: #fd7e14;
}

.existing-image-remove:hover {
    background-color: #e8650e;
}

.image-input {
    margin-bottom: 0;
}

.existing-images-container {
    border: 1px solid #e9ecef;
    padding: 15px;
    border-radius: 5px;
    background-color: #f8f9fa;
}
</style>

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
                        <button type="button" class="image-remove-btn" onclick="removeNewImage(${index})">&times;</button>
                    </div>
                `;
                
                $('#image-preview').append(previewHtml);
            };
            
            reader.readAsDataURL(file);
        }

        // Form submission
        $('#form-update').submit(function(e) {
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

            // Add new images to FormData
            selectedFiles.forEach((file, index) => {
                formData.append('service_images[]', file);
            });

            $.ajax({
                url: '<?= base_url('/admin/service/update/' . $metadata->service_id) ?>',
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
                    
                    let errorMessage = 'Có lỗi xảy ra khi cập nhật dịch vụ';
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

    // Remove new image from preview
    function removeNewImage(index) {
        // Remove from selectedFiles array
        selectedFiles.splice(index, 1);
        
        // Remove preview element
        $(`.image-preview-item[data-index="${index}"]`).remove();
        
        // Update indices for remaining items
        $('.image-preview-item').each(function(newIndex) {
            $(this).attr('data-index', newIndex);
            $(this).find('.image-remove-btn').attr('onclick', `removeNewImage(${newIndex})`);
        });
    }

    // Remove existing image
    function removeExistingImage(imageId) {
        if (!confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
            return;
        }

        $.ajax({
            url: '<?= base_url('/admin/service/image/') ?>' + imageId,
            type: 'DELETE',
            dataType: 'json',
            success: function(response) {
                swAlert('Thông báo', response.message, 'success');
                
                // Remove the image element from DOM
                $(`.existing-image-item[data-image-id="${imageId}"]`).fadeOut(300, function() {
                    $(this).remove();
                    
                    // Check if no existing images left
                    if ($('.existing-image-item').length === 0) {
                        $('.existing-images-container').parent().fadeOut(300);
                    }
                });
            },
            error: function(xhr, status, error) {
                console.log('error', xhr.responseText);
                
                let errorMessage = 'Có lỗi xảy ra khi xóa ảnh';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMessage = errorResponse.message || errorMessage;
                } catch (e) {
                    // Use default error message
                }
                
                swAlert('Thông báo', errorMessage, 'error');
            }
        });
    }
</script>
<?php end_section() ?>
<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<!-- Khối form thêm blog -->
<section class="admin-form-wrapper">
    <form id="form-add-blog" class="admin-form" enctype="multipart/form-data">
        <!-- Tiêu đề blog -->
        <div class="admin-form__group">
            <label for="blogTitle" class="admin-form__label">Tiêu đề (*)</label>
            <input type="text" id="blogTitle" name="title" class="admin-form__input" required>
        </div>

        <!-- Nội dung blog -->
        <div class="admin-form__group">
            <label for="blogContent" class="admin-form__label">Nội dung (*)</label>
            <textarea id="blogContent" name="content" class="admin-form__textarea" required></textarea>
        </div>

        <!-- Tóm tắt -->
        <div class="admin-form__group">
            <label for="blogExcerpt" class="admin-form__label">Tóm tắt</label>
            <textarea id="blogExcerpt" name="excerpt" class="admin-form__textarea" rows="3"></textarea>
        </div>

        <!-- Ảnh đại diện -->
        <div class="admin-form__group">
            <label for="featuredImage" class="admin-form__label">Ảnh đại diện</label>
            <div class="image-upload-container">
                <input type="file" id="featuredImage" name="featured_image" accept="image/*" class="admin-form__input image-input" />
                <div class="image-upload-preview" id="image-preview">
                    <!-- Preview images will be displayed here -->
                </div>
            </div>

            <small class="admin-form__note">Chấp nhận: JPG, JPEG, PNG, GIF, WEBP. Tối đa 5MB</small>
        </div>

        <!-- Trạng thái -->
        <div class="admin-form__group">
            <label for="blogStatus" class="admin-form__label">Trạng thái</label>
            <select id="blogStatus" name="status" class="admin-form__select">
                <option value="draft">Nháp</option>
                <option value="published">Đã xuất bản</option>
                <option value="archived">Lưu trữ</option>
            </select>
        </div>

        <!-- SEO Meta Title -->
        <div class="admin-form__group">
            <label for="metaTitle" class="admin-form__label">Meta Title (SEO)</label>
            <input type="text" id="metaTitle" name="meta_title" class="admin-form__input">
            <small class="admin-form__note">Tối ưu cho SEO, nên từ 50-60 ký tự</small>
        </div>

        <!-- SEO Meta Description -->
        <div class="admin-form__group">
            <label for="metaDescription" class="admin-form__label">Meta Description (SEO)</label>
            <textarea id="metaDescription" name="meta_description" class="admin-form__textarea" rows="3"></textarea>
            <small class="admin-form__note">Mô tả ngắn cho SEO, nên từ 150-160 ký tự</small>
        </div>

        <div class="admin-form__actions">
            <button type="submit" class="btn btn--primary">Thêm blog</button>
            <a href="<?= base_url('/admin/blog') ?>" class="btn btn--secondary">Hủy</a>
        </div>
    </form>
</section>

<?php start_section('links') ?>
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<?php end_section() ?>

<?php start_section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        // Khởi tạo Summernote
        $('#blogContent').summernote({
            height: 400,
            lang: 'vi-VN',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            placeholder: 'Nhập nội dung blog của bạn...',
            tabsize: 2,
            focus: false,
            callbacks: {
                onImageUpload: function(files) {
                    // Xử lý upload ảnh trong editor
                    uploadSummernoteImage(files[0], this);
                }
            }
        });

        // Display image preview
        function displayImagePreview(file, index) {
            const reader = new FileReader();
            if (!file) return;

            $('.image-preview-item').remove();

            reader.onload = function(e) {
                const previewHtml = `
                    <div class="image-preview-item" data-index="${index}">
                        <img src="${e.target.result}" alt="Preview">
                    </div>
                `;

                $('#image-preview').append(previewHtml);
            };

            reader.readAsDataURL(file);
        }

        // Function upload ảnh cho Summernote
        function uploadSummernoteImage(file, editor) {
            const data = new FormData();
            data.append('file', file);

            $.ajax({
                url: '<?= base_url('/admin/blog/upload-image') ?>', // Bạn cần tạo endpoint này
                cache: false,
                contentType: false,
                processData: false,
                data: data,
                type: 'POST',
                success: function(response) {
                    if (response.status) {
                        $(editor).summernote('insertImage', response.metadata.url);
                    } else {
                        swAlert('Lỗi', 'Không thể tải ảnh lên', 'error');
                    }
                },
                error: function() {
                    swAlert('Lỗi', 'Có lỗi xảy ra khi tải ảnh', 'error');
                }
            });
        }

        // Xử lý submit form
        $('#form-add-blog').submit(function(e) {
            e.preventDefault();

            // Tạo FormData để xử lý file upload
            const formData = new FormData();
            formData.append('title', $('#blogTitle').val());
            formData.append('content', $('#blogContent').val());
            formData.append('excerpt', $('#blogExcerpt').val());
            formData.append('status', $('#blogStatus').val());
            formData.append('meta_title', $('#metaTitle').val());
            formData.append('meta_description', $('#metaDescription').val());

            // Thêm file ảnh nếu có
            const featuredImage = $('#featuredImage')[0].files[0];
            if (featuredImage) {
                formData.append('featured_image', featuredImage);
            }

            // Disable submit button
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.text();
            submitBtn.prop('disabled', true).text('Đang xử lý...');

            $.ajax({
                url: '<?= base_url('/admin/blog/add') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
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
                },
                complete: function() {
                    // Re-enable submit button
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });

        // Auto generate meta title from title
        $('#blogTitle').on('input', function() {
            const title = $(this).val();
            if (title && $('#metaTitle').val() === '') {
                $('#metaTitle').val(title);
            }
        });

        // Character counter for meta fields
        function updateCharCounter(input, counter, maxLength) {
            const length = input.val().length;
            counter.text(`${length}/${maxLength} ký tự`);
            if (length > maxLength) {
                counter.addClass('text-danger');
            } else {
                counter.removeClass('text-danger');
            }
        }

        // Add character counters
        $('#metaTitle').after('<small class="char-counter" id="metaTitleCounter"></small>');
        $('#metaDescription').after('<small class="char-counter" id="metaDescCounter"></small>');

        $('#metaTitle').on('input', function() {
            updateCharCounter($(this), $('#metaTitleCounter'), 60);
        });

        $('#metaDescription').on('input', function() {
            updateCharCounter($(this), $('#metaDescCounter'), 160);
        });

        // Initialize counters
        updateCharCounter($('#metaTitle'), $('#metaTitleCounter'), 60);
        updateCharCounter($('#metaDescription'), $('#metaDescCounter'), 160);

        // Preview image before upload
        $('#featuredImage').change(function() {
            const file = this.files[0];
            if (file) {
                // Validate file size
                if (file.size > 5 * 1024 * 1024) {
                    swAlert('Cảnh báo', 'File ảnh quá lớn. Vui lòng chọn file nhỏ hơn 5MB', 'warning');
                    $(this).val('');
                    return;
                }

                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    swAlert('Cảnh báo', 'Định dạng file không được hỗ trợ', 'warning');
                    $(this).val('');
                    return;
                }

                displayImagePreview(file, 0);
            }
        });
    });
</script>

<style>
    .char-counter {
        display: block;
        margin-top: 5px;
        font-size: 12px;
        color: #666;
    }

    .char-counter.text-danger {
        color: #dc3545 !important;
    }

    .admin-form__note {
        display: block;
        margin-top: 5px;
        font-size: 12px;
        color: #666;
    }

    .image-preview img {
        display: block;
        margin-top: 10px;
    }
</style>
<?php end_section() ?>
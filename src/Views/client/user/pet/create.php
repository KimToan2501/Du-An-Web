<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php start_section('links') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/account.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/account-pet.css') ?>">
<?php end_section() ?>

<div class="container">
  <div class="row">
    <!-- Sidebar -->
    <?php include_partial('client/sidebar-profile') ?>

    <!-- Main Content -->
    <div class="col-md-9 p-4">
      <!-- Tab Navigation -->
      <?php include_partial('client/tab-profile') ?>

      <!-- Create Pet Form -->
      <div id="pets-tab" class="tab-content">
        <div class="pets-table-card p-4">
          <div class="pets-header d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-purple">Thêm Thú Cưng Mới</h3>
            <div>
              <a href="<?= base_url('/user/pets') ?>" class="btn btn-outline-purple me-2">
                <i class="fas fa-arrow-left"></i> Quay lại
              </a>
            </div>
          </div>


          <form id="addPetForm" enctype="multipart/form-data">
            <div class="row">
              <!-- Pet Avatar -->
              <div class="col-12 mb-4">
                <div class="text-center">
                  <div class="pet-avatar-upload">
                    <img id="avatarPreview" src="<?= base_url('assets/images/default-pet.jpg') ?>"
                      alt="Pet Avatar" class="rounded-circle mb-3"
                      style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #dee2e6;">
                    <div>
                      <label for="avatar" class="btn btn-outline-purple">
                        <i class="fas fa-camera"></i> Chọn ảnh
                      </label>
                      <input type="file" id="avatar" name="avatar" accept="image/*" class="d-none">
                      <small class="form-text text-muted d-block mt-2">Chọn ảnh đại diện cho thú cưng (JPG, PNG, tối đa 5MB)</small>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Basic Information -->
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label" for="name">Tên thú cưng <span class="text-danger">*</span></label>
                  <input type="text" id="name" name="name" required class="form-control"
                    placeholder="Nhập tên thú cưng" maxlength="100">
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label" for="type">Loại thú cưng <span class="text-danger">*</span></label>
                  <select id="type" name="type" required class="form-select">
                    <option value="">Chọn loại thú cưng</option>
                    <option value="dog">Chó</option>
                    <option value="cat">Mèo</option>
                    <option value="bird">Chim</option>
                    <option value="fish">Cá</option>
                    <option value="rabbit">Thỏ</option>
                    <option value="hamster">Chuột hamster</option>
                    <option value="other">Khác</option>
                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label" for="breed">Giống</label>
                  <input type="text" id="breed" name="breed" class="form-control"
                    placeholder="Nhập giống thú cưng" maxlength="100">
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label" for="gender">Giới tính</label>
                  <select id="gender" name="gender" class="form-select">
                    <option value="">Chọn giới tính</option>
                    <option value="male">Đực</option>
                    <option value="female">Cái</option>
                    <option value="unknown">Không xác định</option>
                  </select>
                </div>
              </div>

              <!-- Age Information -->
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label" for="age">Tuổi</label>
                  <input type="number" id="age" name="age" min="0" max="50" class="form-control"
                    placeholder="Nhập tuổi">
                </div>
              </div>

              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label" for="age_unit">Đơn vị tuổi</label>
                  <select id="age_unit" name="age_unit" class="form-select">
                    <option value="">Chọn đơn vị</option>
                    <option value="days">Ngày</option>
                    <option value="weeks">Tuần</option>
                    <option value="months">Tháng</option>
                    <option value="years">Năm</option>
                  </select>
                </div>
              </div>

              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label" for="weight">Cân nặng (kg)</label>
                  <input type="number" id="weight" name="weight" min="0" step="0.1" class="form-control"
                    placeholder="Nhập cân nặng">
                </div>
              </div>

              <!-- Physical Information -->
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label" for="size">Kích thước</label>
                  <select id="size" name="size" class="form-select">
                    <option value="">Chọn kích thước</option>
                    <option value="small">Nhỏ</option>
                    <option value="medium">Trung bình</option>
                    <option value="large">Lớn</option>
                    <option value="extra_large">Rất lớn</option>
                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label" for="color">Màu sắc</label>
                  <input type="text" id="color" name="color" class="form-control"
                    placeholder="Nhập màu sắc lông/da" maxlength="100">
                </div>
              </div>

              <!-- Medical Notes -->
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label" for="medical_notes">Ghi chú y tế</label>
                  <textarea id="medical_notes" name="medical_notes" rows="3" class="form-control"
                    placeholder="Nhập thông tin về tình trạng sức khỏe, bệnh lý, thuốc đang dùng..."></textarea>
                </div>
              </div>

              <!-- Behavioral Notes -->
              <div class="col-12">
                <div class="mb-3">
                  <label class="form-label" for="behavioral_notes">Ghi chú hành vi</label>
                  <textarea id="behavioral_notes" name="behavioral_notes" rows="3" class="form-control"
                    placeholder="Nhập thông tin về tính cách, hành vi, sở thích của thú cưng..."></textarea>
                </div>
              </div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <button type="submit" class="btn btn-purple">
                <i class="fas fa-save"></i> Thêm thú cưng
              </button>
              <a href="<?= base_url('/user/pets') ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Hủy bỏ
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php start_section('scripts') ?>
<script>
  $(document).ready(function() {
    // Preview avatar when file is selected
    $('#avatar').on('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
          swAlert('Thông báo', 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF)', 'error');
          $(this).val('');
          return;
        }

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
          swAlert('Thông báo', 'Kích thước file không được vượt quá 5MB', 'error');
          $(this).val('');
          return;
        }

        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#avatarPreview').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
      }
    });

    // Form validation and submission
    $('#addPetForm').on('submit', function(e) {
      e.preventDefault();

      // Get form data
      const name = $('#name').val().trim();
      const type = $('#type').val();
      const breed = $('#breed').val().trim();
      const gender = $('#gender').val();
      const age = $('#age').val();
      const ageUnit = $('#age_unit').val();
      const weight = $('#weight').val();
      const size = $('#size').val();
      const color = $('#color').val().trim();
      const medicalNotes = $('#medical_notes').val().trim();
      const behavioralNotes = $('#behavioral_notes').val().trim();

      // Validation
      if (!name) {
        swAlert('Thông báo', 'Vui lòng nhập tên thú cưng', 'error');
        $('#name').focus();
        return;
      }

      if (name.length < 2) {
        swAlert('Thông báo', 'Tên thú cưng phải có ít nhất 2 ký tự', 'error');
        $('#name').focus();
        return;
      }

      if (!type) {
        swAlert('Thông báo', 'Vui lòng chọn loại thú cưng', 'error');
        $('#type').focus();
        return;
      }

      // Validate age if provided
      if (age && !ageUnit) {
        swAlert('Thông báo', 'Vui lòng chọn đơn vị tuổi khi nhập tuổi', 'error');
        $('#age_unit').focus();
        return;
      }

      if (ageUnit && !age) {
        swAlert('Thông báo', 'Vui lòng nhập tuổi khi chọn đơn vị tuổi', 'error');
        $('#age').focus();
        return;
      }

      if (age && (age < 0 || age > 50)) {
        swAlert('Thông báo', 'Tuổi phải từ 0 đến 50', 'error');
        $('#age').focus();
        return;
      }

      if (weight && weight <= 0) {
        swAlert('Thông báo', 'Cân nặng phải lớn hơn 0', 'error');
        $('#weight').focus();
        return;
      }

      // Prepare form data for submission
      const formData = new FormData();
      formData.append('name', name);
      formData.append('type', type);
      formData.append('breed', breed);
      formData.append('gender', gender);
      formData.append('age', age);
      formData.append('age_unit', ageUnit);
      formData.append('weight', weight);
      formData.append('size', size);
      formData.append('color', color);
      formData.append('medical_notes', medicalNotes);
      formData.append('behavioral_notes', behavioralNotes);

      // Add avatar file if selected
      const avatarFile = $('#avatar')[0].files[0];
      if (avatarFile) {
        formData.append('avatar', avatarFile);
      }

      // Disable submit button to prevent double submission
      const submitBtn = $(this).find('button[type="submit"]');
      const originalText = submitBtn.html();
      submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');

      loadAjaxStatus('start');

      // Submit form via AJAX
      $.ajax({
        url: '<?= base_url('/user/pets') ?>',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          swAlert('Thông báo', response.message || 'Thêm thú cưng thành công!', 'success');
          setTimeout(function() {
            window.location.href = '<?= base_url('/user/pets') ?>';
          }, 1500);
        },
        error: function(xhr) {
          let errorMessage = 'Đã xảy ra lỗi khi thêm thú cưng';

          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          } else if (xhr.responseJSON && xhr.responseJSON.errors) {
            // Handle validation errors
            const errors = xhr.responseJSON.errors;
            const errorMessages = Object.values(errors).flat();
            errorMessage = errorMessages.join('\n');
          }

          swAlert('Thông báo', errorMessage, 'error');
        },
        complete: function() {
          // Re-enable submit button
          submitBtn.prop('disabled', false).html(originalText);
          loadAjaxStatus('stop');
        }
      });
    });

    // Auto-populate breed suggestions based on pet type
    $('#type').on('change', function() {
      const type = $(this).val();
      const breedInput = $('#breed');

      // Clear current value
      breedInput.val('');

      // Add placeholder based on type
      switch (type) {
        case 'dog':
          breedInput.attr('placeholder', 'VD: Golden Retriever, Poodle, Husky...');
          break;
        case 'cat':
          breedInput.attr('placeholder', 'VD: Persian, Siamese, Maine Coon...');
          break;
        case 'bird':
          breedInput.attr('placeholder', 'VD: Vẹt, Chim cảnh, Chim yến...');
          break;
        case 'fish':
          breedInput.attr('placeholder', 'VD: Cá betta, Cá vàng, Cá cảnh...');
          break;
        case 'rabbit':
          breedInput.attr('placeholder', 'VD: Thỏ lop, Thỏ Angora, Thỏ Lionhead...');
          break;
        default:
          breedInput.attr('placeholder', 'Nhập giống thú cưng');
      }
    });
  });
</script>
<?php end_section() ?>
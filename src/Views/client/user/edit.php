<?php
// edit.php - Trang chỉnh sửa profile
use App\Core\Auth;

$auth = Auth::getInstance();
$user = $auth->user();

if (!$user) {
  redirect('/login');
}
?>

<?php start_section('title'); ?>
Chỉnh sửa thông tin
<?php end_section(); ?>

<?php start_section('links'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/account.css') ?>">
<style>
  .edit-form-container {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
  }

  .form-section-title {
    color: var(--primary-purple);
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--primary-purple);
  }

  .avatar-upload-container {
    text-align: center;
    margin-bottom: 2rem;
  }

  .avatar-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--primary-purple);
    margin-bottom: 1rem;
  }

  .upload-btn {
    background: var(--primary-purple);
    color: white;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .upload-btn:hover {
    background: var(--dark-purple);
    transform: translateY(-2px);
  }

  .form-group {
    margin-bottom: 1.5rem;
  }

  .form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    display: block;
  }

  .form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s ease;
    width: 100%;
  }

  .form-control:focus {
    border-color: var(--primary-purple);
    box-shadow: 0 0 0 0.2rem rgba(139, 69, 189, 0.25);
    outline: none;
  }

  .btn-primary {
    background: var(--primary-purple);
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-primary:hover {
    background: var(--dark-purple);
    transform: translateY(-2px);
  }

  .btn-secondary {
    background: #6c757d;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 25px;
    font-weight: 600;
    color: white;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
  }

  .btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
    color: white;
    text-decoration: none;
  }

  .error-message {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: none;
  }

  .success-message {
    color: #28a745;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: none;
  }

  .password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
  }

  .password-input-container {
    position: relative;
  }

  @media (max-width: 768px) {
    .edit-form-container {
      padding: 1rem;
    }

    .avatar-preview {
      width: 100px;
      height: 100px;
    }
  }
</style>
<?php end_section(); ?>

<div class="container">
  <div class="row">
    <!-- Sidebar -->
    <?php include_partial('client/sidebar-profile') ?>

    <!-- Main Content -->
    <div class="col-md-9 p-4">
      <!-- Back Button -->
      <div class="mb-3">
        <a href="/user/profile" class="btn-secondary">
          <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
      </div>

      <!-- Edit Profile Form -->
      <div class="edit-form-container">
        <h3 class="form-section-title">
          <i class="fas fa-user-edit me-2"></i>Chỉnh sửa thông tin cá nhân
        </h3>

        <form id="editProfileForm" enctype="multipart/form-data">
          <!-- Avatar Upload -->
          <div class="avatar-upload-container">
            <img src="<?= $auth->avatar() ?>" alt="Avatar" class="avatar-preview" id="avatarPreview">
            <div>
              <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;">
              <button type="button" class="upload-btn" onclick="document.getElementById('avatarInput').click()">
                <i class="fas fa-camera me-2"></i>Thay đổi ảnh đại diện
              </button>
            </div>
            <div class="error-message" id="avatarError"></div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label" for="name">
                  <i class="fas fa-user me-2"></i>Họ tên *
                </label>
                <input type="text"
                  class="form-control"
                  id="name"
                  name="name"
                  value="<?= htmlspecialchars($user['name']) ?>"
                  required>
                <div class="error-message" id="nameError"></div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label" for="email">
                  <i class="fas fa-envelope me-2"></i>Email *
                </label>
                <input type="email"
                  class="form-control"
                  id="email"
                  name="email"
                  value="<?= htmlspecialchars($user['email']) ?>"
                  required>
                <div class="error-message" id="emailError"></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label" for="phone">
                  <i class="fas fa-phone me-2"></i>Số điện thoại
                </label>
                <input type="tel"
                  class="form-control"
                  id="phone"
                  name="phone"
                  value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                <div class="error-message" id="phoneError"></div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label" for="address">
                  <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ
                </label>
                <input type="text"
                  class="form-control"
                  id="address"
                  name="address"
                  value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                <div class="error-message" id="addressError"></div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-3">
            <a href="/user/profile" class="btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-2"></i>Lưu thay đổi
            </button>
          </div>

          <div class="success-message mt-3" id="profileSuccess"></div>
        </form>
      </div>

      <!-- Change Password Form -->
      <div class="edit-form-container">
        <h3 class="form-section-title">
          <i class="fas fa-lock me-2"></i>Đổi mật khẩu
        </h3>

        <form id="changePasswordForm">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label" for="currentPassword">
                  <i class="fas fa-key me-2"></i>Mật khẩu hiện tại *
                </label>
                <div class="password-input-container">
                  <input type="password"
                    class="form-control"
                    id="currentPassword"
                    name="currentPassword"
                    required>
                  <button type="button" class="password-toggle" onclick="togglePassword('currentPassword')">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
                <div class="error-message" id="currentPasswordError"></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label" for="newPassword">
                  <i class="fas fa-lock me-2"></i>Mật khẩu mới *
                </label>
                <div class="password-input-container">
                  <input type="password"
                    class="form-control"
                    id="newPassword"
                    name="newPassword"
                    required>
                  <button type="button" class="password-toggle" onclick="togglePassword('newPassword')">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
                <div class="error-message" id="newPasswordError"></div>
                <small class="text-muted">Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số</small>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label" for="confirmPassword">
                  <i class="fas fa-lock me-2"></i>Xác nhận mật khẩu mới *
                </label>
                <div class="password-input-container">
                  <input type="password"
                    class="form-control"
                    id="confirmPassword"
                    name="confirmPassword"
                    required>
                  <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
                <div class="error-message" id="confirmPasswordError"></div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-key me-2"></i>Đổi mật khẩu
            </button>
          </div>

          <div class="success-message mt-3" id="passwordSuccess"></div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php start_section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    // Avatar preview
    $('#avatarInput').change(function() {
      const file = this.files[0];
      if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
          showError('avatarError', 'Vui lòng chọn file hình ảnh');
          return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
          showError('avatarError', 'Kích thước file không được vượt quá 5MB');
          return;
        }

        hideError('avatarError');

        const reader = new FileReader();
        reader.onload = function(e) {
          $('#avatarPreview').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
      }
    });

    // Edit Profile Form Validation
    $('#editProfileForm').submit(function(e) {
      e.preventDefault();

      if (validateProfileForm()) {
        submitProfileForm();
      }
    });

    // Change Password Form Validation
    $('#changePasswordForm').submit(function(e) {
      e.preventDefault();

      if (validatePasswordForm()) {
        submitPasswordForm();
      }
    });

    // Real-time validation
    $('#name').blur(function() {
      validateName();
    });

    $('#email').blur(function() {
      validateEmail();
    });

    $('#phone').blur(function() {
      validatePhone();
    });

    $('#newPassword').blur(function() {
      validateNewPassword();
    });

    $('#confirmPassword').blur(function() {
      validateConfirmPassword();
    });
  });

  // Validation Functions
  function validateProfileForm() {
    let isValid = true;

    if (!validateName()) isValid = false;
    if (!validateEmail()) isValid = false;
    if (!validatePhone()) isValid = false;

    return isValid;
  }

  function validatePasswordForm() {
    let isValid = true;

    if (!validateCurrentPassword()) isValid = false;
    if (!validateNewPassword()) isValid = false;
    if (!validateConfirmPassword()) isValid = false;

    return isValid;
  }

  function validateName() {
    const name = $('#name').val().trim();

    if (name.length === 0) {
      showError('nameError', 'Vui lòng nhập họ tên');
      return false;
    }

    if (name.length < 2) {
      showError('nameError', 'Họ tên phải có ít nhất 2 ký tự');
      return false;
    }

    if (name.length > 50) {
      showError('nameError', 'Họ tên không được vượt quá 50 ký tự');
      return false;
    }

    hideError('nameError');
    return true;
  }

  function validateEmail() {
    const email = $('#email').val().trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email.length === 0) {
      showError('emailError', 'Vui lòng nhập email');
      return false;
    }

    if (!emailRegex.test(email)) {
      showError('emailError', 'Email không hợp lệ');
      return false;
    }

    hideError('emailError');
    return true;
  }

  function validatePhone() {
    const phone = $('#phone').val().trim();

    if (phone.length > 0) {
      const phoneRegex = /^[0-9]{10,11}$/;
      if (!phoneRegex.test(phone)) {
        showError('phoneError', 'Số điện thoại phải có 10-11 chữ số');
        return false;
      }
    }

    hideError('phoneError');
    return true;
  }

  function validateCurrentPassword() {
    const currentPassword = $('#currentPassword').val();

    if (currentPassword.length === 0) {
      showError('currentPasswordError', 'Vui lòng nhập mật khẩu hiện tại');
      return false;
    }

    hideError('currentPasswordError');
    return true;
  }

  function validateNewPassword() {
    const newPassword = $('#newPassword').val();
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/;

    if (newPassword.length === 0) {
      showError('newPasswordError', 'Vui lòng nhập mật khẩu mới');
      return false;
    }

    if (!passwordRegex.test(newPassword)) {
      showError('newPasswordError', 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số');
      return false;
    }

    hideError('newPasswordError');
    return true;
  }

  function validateConfirmPassword() {
    const newPassword = $('#newPassword').val();
    const confirmPassword = $('#confirmPassword').val();

    if (confirmPassword.length === 0) {
      showError('confirmPasswordError', 'Vui lòng xác nhận mật khẩu mới');
      return false;
    }

    if (newPassword !== confirmPassword) {
      showError('confirmPasswordError', 'Mật khẩu xác nhận không khớp');
      return false;
    }

    hideError('confirmPasswordError');
    return true;
  }

  // Helper Functions
  function showError(elementId, message) {
    $('#' + elementId).text(message).show();
    $('#' + elementId.replace('Error', '')).addClass('is-invalid');
  }

  function hideError(elementId) {
    $('#' + elementId).hide();
    $('#' + elementId.replace('Error', '')).removeClass('is-invalid');
  }

  function showSuccess(elementId, message) {
    $('#' + elementId).text(message).show();
    setTimeout(function() {
      $('#' + elementId).hide();
    }, 5000);
  }

  // Toggle Password Visibility
  function togglePassword(inputId) {
    const input = $('#' + inputId);
    const icon = input.siblings('.password-toggle').find('i');

    if (input.attr('type') === 'password') {
      input.attr('type', 'text');
      icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
      input.attr('type', 'password');
      icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
  }

  // Submit Functions
  function submitProfileForm() {
    const formData = new FormData($('#editProfileForm')[0]);

    // Show loading state
    const submitBtn = $('#editProfileForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Đang lưu...').prop('disabled', true);

    $.ajax({
      url: '/api/user/update-profile',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        console.log(response);
        response = JSON.parse(response);
        // Handle success response

        if (response.status === 200) {
          swAlert('Thông báo', 'Cập nhật thông tin thành công!', 'success');
          // Optionally reload user data or redirect
          setTimeout(function() {
            window.location.href = '/user/profile';
          }, 2000);
        } else {
          alert('Có lỗi xảy ra: ' + (response.message || 'Vui lòng thử lại'));
        }
      },
      error: function(xhr) {
        const response = JSON.parse(xhr.responseText);
        if (response.errors) {
          // Show field-specific errors
          Object.keys(response.errors).forEach(function(field) {
            showError(field + 'Error', response.errors[field]);
          });
        } else {
          alert('Có lỗi xảy ra, vui lòng thử lại');
        }
      },
      complete: function() {
        submitBtn.html(originalText).prop('disabled', false);
      }
    });
  }

  function submitPasswordForm() {
    const formData = {
      currentPassword: $('#currentPassword').val(),
      newPassword: $('#newPassword').val(),
      confirmPassword: $('#confirmPassword').val()
    };

    // Show loading state
    const submitBtn = $('#changePasswordForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Đang đổi...').prop('disabled', true);

    $.ajax({
      url: '/api/user/change-password',
      type: 'POST',
      data: formData,
      success: function(response) {
        if (response.status === 200) {
          swAlert('Thông báo', 'Đổi mật khẩu thành công!', 'success');
          $('#changePasswordForm')[0].reset();
        } else {
          swAlert("Thông báo", 'Có lỗi xảy ra: ' + (response.message || 'Vui lòng thử lại'), 'error');
        }
      },
      error: function(xhr) {
        const response = JSON.parse(xhr.responseText);
        if (response.errors) {
          // Show field-specific errors
          Object.keys(response.errors).forEach(function(field) {
            showError(field + 'Error', response.errors[field]);
          });
        } else {
          swAlert('Thông báo', 'Có lỗi xảy ra, vui lòng thử lại', 'error');
        }
      },
      complete: function() {
        submitBtn.html(originalText).prop('disabled', false);
      }
    });
  }
</script>
<?php end_section(); ?>
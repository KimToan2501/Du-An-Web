<?php start_section('title') ?>
Đặt lại mật khẩu
<?php end_section() ?>

<?php start_section('links') ?>
<link rel="stylesheet" href="<?= base_url('/assets/css/form.css') ?>">
<link rel="stylesheet" href="<?= base_url('/assets/css/forgot-password.css') ?>">
<link rel="stylesheet" href="<?= base_url('/assets/css/login.css') ?>">
<?php end_section() ?>

<!-- Start: Reset Password Form -->
<form id="reset-password-form" class="pawspa-form__body" action="#">

  <!-- Start: Header -->
  <div class="pawspa-form__header">
    <h3 class="pawspa-form__brand">Pawspa</h3>
    <p class="pawspa-form__description">
      Nhập mật khẩu mới cho tài khoản của bạn.
    </p>
    <img src="<?= base_url('/assets/images/Union01.svg') ?>" alt="" class="pawspa-form__icon">
  </div>
  <!-- End: Header -->

  <!-- Hidden token field -->
  <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

  <!-- Start: Inputs -->
  <div class="pawspa-form__group">
    <input type="password" name="password" class="pawspa-form__input"
      placeholder="Mật khẩu mới" required minlength="6" aria-label="Mật khẩu mới">
  </div>

  <div class="pawspa-form__group">
    <input type="password" name="confirm_password" class="pawspa-form__input"
      placeholder="Nhập lại mật khẩu mới" required minlength="6" aria-label="Nhập lại mật khẩu mới">
  </div>

  <!-- Start: Actions -->
  <div class="pawspa-form__actions">
    <input type="submit" value="Cập nhật mật khẩu" class="pawspa-btn pawspa-btn--submit">

    <p class="pawspa-form__link-wrapper">
      <a href="<?= base_url('/login') ?>" class="pawspa-form__link--back">← Quay lại đăng nhập</a>
    </p>
  </div>
  <!-- End: Actions -->

</form>
<!-- End: Reset Password Form -->

<!-- Start: Background Visual -->
<div class="pawspa-form__background pawspa-form__background--forgot">
  <p class="pawspa-form__slogan">
    Đồng hành cùng bạn & thú cưng – mọi lúc, mọi nơi.
  </p>
  <div class="pawspa-form__background-img">
    <img src="<?= base_url('/assets/images/pngwing.svg') ?>" alt="Chó con giơ tay chào">
  </div>
</div>
<!-- End: Background Visual -->

<?php start_section('scripts') ?>
<script>
  $(document).ready(function() {
    $('#reset-password-form').submit(function(e) {
      e.preventDefault();

      const token = $(this).find('input[name="token"]').val();
      const password = $(this).find('input[name="password"]').val();
      const confirmPassword = $(this).find('input[name="confirm_password"]').val();

      // Validation
      if (!password || !confirmPassword) {
        swAlert('Thông báo', 'Vui lòng nhập đầy đủ thông tin', 'warning');
        return;
      }

      // Kiểm tra mật khẩu
      if (password.length < 6) {
        swAlert('Thông báo', 'Mật khẩu phải có ít nhất 6 ký tự', 'warning');
        return;
      }

      if (password !== confirmPassword) {
        swAlert('Thông báo', 'Mật khẩu xác nhận không khớp', 'warning');
        return;
      }

      const data = {
        token: token,
        password: password,
        confirm_password: confirmPassword
      };

      // Disable submit button
      const submitBtn = $(this).find('input[type="submit"]');
      const originalValue = submitBtn.val();
      submitBtn.prop('disabled', true).val('Đang xử lý...');

      $.ajax({
        url: '<?= base_url('/reset-password') ?>',
        dataType: 'json',
        contentType: 'application/json',
        type: 'POST',
        data: JSON.stringify(data),
        success: function(response) {
          swAlert('Thông báo', response.message, 'success');

          setTimeout(() => {
            if (response.metadata && response.metadata.redirect_url) {
              window.location.href = '<?= base_url() ?>' + response.metadata.redirect_url;
            }
          }, 1500);
        },
        error: function(xhr, status, error) {
          const errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Có lỗi xảy ra';
          swAlert('Thông báo', errorMessage, 'error');
        },
        complete: function() {
          // Re-enable submit button
          submitBtn.prop('disabled', false).val(originalValue);
        }
      });
    });
  });
</script>
<?php end_section() ?>
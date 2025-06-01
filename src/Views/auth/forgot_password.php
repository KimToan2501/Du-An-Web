<?php start_section('title') ?>
Quên mật khẩu
<?php end_section() ?>

<?php start_section('links') ?>
<link rel="stylesheet" href="<?= base_url('/assets/css/form.css') ?>">
<link rel="stylesheet" href="<?= base_url('/assets/css/forgot-password.css') ?>">
<link rel="stylesheet" href="<?= base_url('/assets/css/login.css') ?>">
<?php end_section() ?>

<!-- Start: Forgot Password Form -->
<form id="forgot-password-form" class="pawspa-form__body" action="#">

  <!-- Start: Header -->
  <div class="pawspa-form__header">
    <h3 class="pawspa-form__brand">Pawspa</h3>
    <p class="pawspa-form__description">
      Đừng lo, chúng tôi sẽ giúp bạn khôi phục lại ngay.
    </p>
    <img src="<?= base_url('/assets/images/Union01.svg') ?>" alt="" class="pawspa-form__icon">
  </div>
  <!-- End: Header -->

  <!-- Start: Input -->
  <div class="pawspa-form__group">
    <input type="email" name="email" class="pawspa-form__input" placeholder="Nhập email của bạn"
      required aria-label="Email">
  </div>

  <!-- Start: Actions -->
  <div class="pawspa-form__actions">
    <input type="submit" value="Gửi liên kết khôi phục" class="pawspa-btn pawspa-btn--submit">

    <p class="pawspa-form__link-wrapper">
      <a href="<?= base_url('/login') ?>" class="pawspa-form__link--back">← Quay lại đăng nhập</a>
    </p>
  </div>
  <!-- End: Actions -->

</form>
<!-- End: Forgot Password Form -->

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
    $('#forgot-password-form').submit(function(e) {
      e.preventDefault();

      const email = $(this).find('input[name="email"]').val().trim();

      // Validation
      if (!email) {
        swAlert('Thông báo', 'Vui lòng nhập email', 'warning');
        return;
      }

      // Kiểm tra email format
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        swAlert('Thông báo', 'Email không hợp lệ', 'warning');
        return;
      }

      const data = {
        email: email
      };

      // Disable submit button
      const submitBtn = $(this).find('input[type="submit"]');
      const originalValue = submitBtn.val();
      submitBtn.prop('disabled', true).val('Đang xử lý...');

      $.ajax({
        url: '<?= base_url('/forgot-password') ?>',
        dataType: 'json',
        contentType: 'application/json',
        type: 'POST',
        data: JSON.stringify(data),
        success: function(response) {
          swAlert('Thông báo', response.message, 'success');

          // Reset form
          $('#forgot-password-form')[0].reset();
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
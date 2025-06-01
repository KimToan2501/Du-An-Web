<?php start_section('title') ?>
Đăng nhập
<?php end_section() ?>

<?php start_section('links') ?>
<link rel="stylesheet" href="<?= base_url('/assets/css/form.css') ?>">
<link rel="stylesheet" href="<?= base_url('/assets/css/login.css') ?>">
<?php end_section() ?>

<form class="pawspa-form__body" id="form-login">

  <!-- Start: Header -->
  <div class="pawspa-form__header">
    <h3 class="pawspa-form__brand">Pawspa</h3>
    <p class="pawspa-form__description">Nơi mang đến dịch vụ chăm sóc thú cưng uy tín hàng đầu Việt Nam</p>
    <img src="<?= base_url('/assets/images/Union01.svg') ?>" alt="" class="pawspa-form__icon">
  </div>

  <!-- Start: Input - Username -->
  <div class="pawspa-form__group">
    <input type="email" class="pawspa-form__input" placeholder="Nhập email của bạn" name="email">
  </div>

  <!-- Start: Input - Password -->
  <div class="pawspa-form__group">
    <input type="password" class="pawspa-form__input" placeholder="Nhập mật khẩu" name="password">
  </div>

  <!-- Start: Remember + Forgot Password -->
  <div class="pawspa-form__options">
    <div class="pawspa-form__checkbox pawspa-form__remember">
      <input type="checkbox" id="remember-me" class="pawspa-form__checkbox-input">
      <label for="remember-me" class="pawspa-form__checkbox-label">Luôn ghi nhớ?</label>
    </div>
    <div class="pawspa-form__forgot">
      <a href="<?= base_url('/forgot-password') ?>" class="pawspa-form__link--forgot">Quên mật khẩu?</a>
    </div>
  </div>

  <!-- Start: Actions -->
  <div class="pawspa-form__actions">
    <input type="submit" value="Đăng nhập" class="pawspa-btn pawspa-btn--primary">

    <p class="pawspa-form__link-wrapper">
      Bạn chưa có tài khoản ?
      <a href="<?= base_url('/register') ?>" class="pawspa-form__link--highlight">Đăng ký</a>
    </p>

    <div class="pawspa-form__separator">
      <span>Or</span>
    </div>

    <a href="#" class="pawspa-btn pawspa-btn--outline pawspa-btn--social">
      <img src="<?= base_url('/assets/images/icons/social-media/google.svg') ?>" alt="">
      Đăng nhập bằng google
    </a>
    <a href="#" class="pawspa-btn pawspa-btn--outline pawspa-btn--social">
      <img src="<?= base_url('/assets/images/icons/social-media/facebook.svg') ?>" alt="">
      Đăng nhập bằng facebook
    </a>
  </div>

</form>

<!-- Start: Background Visual -->
<div class="pawspa-form__background pawspa-form__background--login">
  <p class="pawspa-form__slogan">
    Healthy pets bring joy and enrich your life.
  </p>
  <div class="pawspa-form__background-img">
    <img src="<?= base_url('/assets/images/pngwing.svg" alt="Chó con giơ tay chào') ?>">
  </div>
</div>


<?php start_section('scripts') ?>
<script>
  $(document).ready(function() {
    $('#form-login').submit(function(e) {
      e.preventDefault();

      const email = $(this).find('input[name="email"]').val();
      const password = $(this).find('input[name="password"]').val();

      if (!email || !password) {
        swAlert('Thông báo', 'Vui lòng nhập đầy đủ thông tin', 'warning')
        return;
      }

      const data = {
        email: email,
        password: password
      }

      $.ajax({
        url: '<?= base_url('/login') ?>',
        dataType: 'json',
        contentType: 'application/json',
        type: 'POST',
        data: JSON.stringify(data),
        success: function(response) {
          swAlert('Thông báo', response.message, 'success')

          setTimeout(() => {
            window.location.reload()
          }, 1000)
        },
        error: function(xhr, status, error) {
          swAlert('Thông báo', xhr.responseJSON.message, 'error')
        }
      })
    })
  })
</script>
<?php end_section() ?>
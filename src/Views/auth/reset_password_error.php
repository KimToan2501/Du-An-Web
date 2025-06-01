<?php start_section('title') ?>
Lỗi khôi phục mật khẩu
<?php end_section() ?>

<?php start_section('links') ?>
<link rel="stylesheet" href="<?= base_url('/assets/css/form.css') ?>">
<link rel="stylesheet" href="<?= base_url('/assets/css/forgot-password.css') ?>">
<link rel="stylesheet" href="<?= base_url('/assets/css/login.css') ?>">
<?php end_section() ?>

<!-- Start: Error Message -->
<div class="pawspa-form__body">

  <!-- Start: Header -->
  <div class="pawspa-form__header">
    <h3 class="pawspa-form__brand">Pawspa</h3>
    <p class="pawspa-form__description" style="color: #dc3545;">
      <?= htmlspecialchars($error) ?>
    </p>
    <img src="<?= base_url('/assets/images/Union01.svg') ?>" alt="" class="pawspa-form__icon">
  </div>
  <!-- End: Header -->

  <!-- Start: Actions -->
  <div class="pawspa-form__actions">
    <a href="<?= base_url('/forgot-password') ?>" class="pawspa-btn pawspa-btn--submit">
      Yêu cầu liên kết mới
    </a>

    <p class="pawspa-form__link-wrapper">
      <a href="<?= base_url('/login') ?>" class="pawspa-form__link--back">← Quay lại đăng nhập</a>
    </p>
  </div>
  <!-- End: Actions -->

</div>
<!-- End: Error Message -->

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
<?php start_section('title') ?>
Đăng ký
<?php end_section() ?>

<?php start_section('links') ?>
<link rel="stylesheet" href="<?= base_url('/assets/css/form.css') ?>">
<link rel="stylesheet" href="<?= base_url('/assets/css/login.css') ?>">
<?php end_section() ?>

<form id="register-form" class="pawspa-form__body" action="#">
    <!-- Start: Header -->
    <div class="pawspa-form__header">
        <h3 class="pawspa-form__brand">Đăng ký</h3>
        <p class="pawspa-form__description">Nơi mang đến dịch vụ chăm sóc thú cưng uy tín hàng đầu Việt Nam</p>
        <img src="<?= base_url('/assets/images/Union01.svg') ?>" alt="" class="pawspa-form__icon">
    </div>
    <!-- End: Header -->

    <div class="pawspa-form__group">
        <input type="text" name="name" class="pawspa-form__input" placeholder="Họ và tên" required
            aria-label="Họ và tên">
    </div>

    <div class="pawspa-form__group">
        <input id="phone" name="phone" type="tel" class="pawspa-form__input" placeholder="Số điện thoại"
            required>
    </div>

    <div class="pawspa-form__group">
        <input type="email" name="email" class="pawspa-form__input" placeholder="Email" required aria-label="Email">
    </div>

    <div class="pawspa-form__group">
        <input type="password" name="password" class="pawspa-form__input" placeholder="Mật khẩu" required minlength="6"
            aria-label="Mật khẩu">
    </div>

    <div class="pawspa-form__group">
        <input type="password" name="confirm_password" class="pawspa-form__input" placeholder="Nhập lại mật khẩu" required
            minlength="6" aria-label="Nhập lại mật khẩu">
    </div>

    <!-- Start: Actions -->
    <div class="pawspa-form__actions">
        <input type="submit" value="Xác nhận" class="pawspa-btn pawspa-btn--primary">

        <p class="pawspa-form__link-wrapper">
            Bạn đã có tài khoản ?
            <a href="<?= base_url('/login') ?>" class="pawspa-register__link--highlight">Đăng nhập</a>
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
    <!-- End: Actions -->
</form>

<!-- Start: Background Visual -->
<div class="pawspa-form__background pawspa-form__background--login">
    <p class="pawspa-form__slogan">
        Healthy pets bring joy and enrich your life.
    </p>
    <div class="pawspa-form__background-img">
        <img src="<?= base_url('/assets/images/pngwing.svg') ?>" alt="Chó con giơ tay chào">
    </div>
</div>

<?php start_section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#register-form').submit(function(e) {
            e.preventDefault();

            const name = $(this).find('input[name="name"]').val().trim();
            const phone = $(this).find('input[name="phone"]').val().trim();
            const email = $(this).find('input[name="email"]').val().trim();
            const password = $(this).find('input[name="password"]').val();
            const confirmPassword = $(this).find('input[name="confirm_password"]').val();

            // Validation
            if (!name || !phone || !email || !password || !confirmPassword) {
                swAlert('Thông báo', 'Vui lòng nhập đầy đủ thông tin', 'warning');
                return;
            }

            // Kiểm tra email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                swAlert('Thông báo', 'Email không hợp lệ', 'warning');
                return;
            }

            // Kiểm tra số điện thoại Việt Nam
            const phoneRegex = /^(0[1|2|3|5|6|7|8|9])+([0-9]{8})$/;
            if (!phoneRegex.test(phone)) {
                swAlert('Thông báo', 'Số điện thoại không hợp lệ', 'warning');
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
                name: name,
                phone: phone,
                email: email,
                password: password,
                confirm_password: confirmPassword
            };

            // Disable submit button
            const submitBtn = $(this).find('input[type="submit"]');
            const originalValue = submitBtn.val();
            submitBtn.prop('disabled', true).val('Đang xử lý...');

            $.ajax({
                url: '<?= base_url('/register') ?>',
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
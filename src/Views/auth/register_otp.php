<?php start_section('title') ?>
Xác thực OTP
<?php end_section() ?>

<?php start_section('links') ?>
<link rel="stylesheet" href="<?= base_url('/assets/css/form.css') ?>">
<link rel="stylesheet" href="<?= base_url('/assets/css/login.css') ?>">
<style>
  .otp-input-group {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin: 20px 0;
  }

  .otp-input {
    width: 50px;
    height: 50px;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    border: 2px solid #ddd;
    border-radius: 8px;
    outline: none;
    transition: border-color 0.3s;
  }

  .otp-input:focus {
    border-color: #007bff;
  }

  .otp-input.filled {
    border-color: #28a745;
    background-color: #f8fff9;
  }

  .resend-section {
    text-align: center;
    margin: 15px 0;
  }

  .resend-btn {
    background: none;
    border: none;
    color: #007bff;
    text-decoration: underline;
    cursor: pointer;
    font-size: 14px;
  }

  .resend-btn:disabled {
    color: #666;
    cursor: not-allowed;
    text-decoration: none;
  }

  .countdown {
    color: #666;
    font-size: 14px;
  }
</style>
<?php end_section() ?>

<form id="otp-form" class="pawspa-form__body" action="#">
  <!-- Start: Header -->
  <div class="pawspa-form__header">
    <h3 class="pawspa-form__brand">Pawspa</h3>
    <p class="pawspa-form__description">Xác thực tài khoản</p>
    <img src="<?= base_url('/assets/images/Union01.svg') ?>" alt="" class="pawspa-form__icon">
  </div>

  <div class="pawspa-form__group">
    <p style="text-align: center; color: #666; margin-bottom: 20px;">
      Mã xác thực đã được gửi đến email:<br>
      <strong><?= htmlspecialchars($email) ?></strong>
    </p>
  </div>

  <!-- OTP Input -->
  <div class="pawspa-form__group">
    <label style="display: block; text-align: center; margin-bottom: 10px; font-weight: 500;">
      Nhập mã xác thực (6 số)
    </label>
    <div class="otp-input-group">
      <input type="text" class="otp-input" maxlength="1" data-index="0">
      <input type="text" class="otp-input" maxlength="1" data-index="1">
      <input type="text" class="otp-input" maxlength="1" data-index="2">
      <input type="text" class="otp-input" maxlength="1" data-index="3">
      <input type="text" class="otp-input" maxlength="1" data-index="4">
      <input type="text" class="otp-input" maxlength="1" data-index="5">
    </div>
  </div>

  <!-- Resend Section -->
  <div class="resend-section">
    <div class="countdown" id="countdown">Gửi lại mã sau: <span id="countdown-timer">300</span>s</div>
    <button type="button" class="resend-btn" id="resend-btn" disabled>Gửi lại mã xác thực</button>
  </div>

  <!-- Actions -->
  <div class="pawspa-form__actions">
    <input type="submit" value="Xác thực" class="pawspa-btn pawspa-btn--primary">

    <p class="pawspa-form__link-wrapper">
      <a href="<?= base_url('/register') ?>" class="pawspa-form__link--highlight">Quay lại đăng ký</a>
    </p>
  </div>
</form>

<!-- Background Visual -->
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
    let countdownTimer;
    let timeLeft = 300; // 5 minutes

    // OTP Input handling
    $('.otp-input').on('input', function() {
      const $this = $(this);
      const index = parseInt($this.data('index'));
      const value = $this.val();

      // Only allow numbers
      if (!/^\d*$/.test(value)) {
        $this.val('');
        return;
      }

      // Add filled class
      if (value) {
        $this.addClass('filled');
      } else {
        $this.removeClass('filled');
      }

      // Auto focus next input
      if (value && index < 5) {
        $('.otp-input[data-index="' + (index + 1) + '"]').focus();
      }

      // Auto submit when all inputs are filled
      if (getOtpValue().length === 6) {
        $('#otp-form').submit();
      }
    });

    // Handle backspace
    $('.otp-input').on('keydown', function(e) {
      const $this = $(this);
      const index = parseInt($this.data('index'));

      if (e.key === 'Backspace' && !$this.val() && index > 0) {
        $('.otp-input[data-index="' + (index - 1) + '"]').focus();
      }
    });

    // Handle paste
    $('.otp-input').on('paste', function(e) {
      e.preventDefault();
      const pastedData = e.originalEvent.clipboardData.getData('text');
      const numbers = pastedData.replace(/\D/g, '').slice(0, 6);

      $('.otp-input').each(function(index) {
        if (numbers[index]) {
          $(this).val(numbers[index]).addClass('filled');
        }
      });

      if (numbers.length === 6) {
        $('#otp-form').submit();
      }
    });

    // Get OTP value
    function getOtpValue() {
      let otp = '';
      $('.otp-input').each(function() {
        otp += $(this).val();
      });
      return otp;
    }

    // Countdown timer
    function startCountdown() {
      countdownTimer = setInterval(function() {
        timeLeft--;
        $('#countdown-timer').text(timeLeft);

        if (timeLeft <= 0) {
          clearInterval(countdownTimer);
          $('#countdown').hide();
          $('#resend-btn').prop('disabled', false);
        }
      }, 1000);
    }

    // Start countdown on page load
    startCountdown();

    // Form submission
    $('#otp-form').submit(function(e) {
      e.preventDefault();

      const otp = getOtpValue();

      if (otp.length !== 6) {
        swAlert('Thông báo', 'Vui lòng nhập đầy đủ mã OTP', 'warning');
        return;
      }

      const data = {
        otp: otp
      };

      // Disable submit button
      const submitBtn = $(this).find('input[type="submit"]');
      const originalValue = submitBtn.val();
      submitBtn.prop('disabled', true).val('Đang xác thực...');

      $.ajax({
        url: '<?= base_url('/register/otp/verify') ?>',
        dataType: 'json',
        contentType: 'application/json',
        type: 'POST',
        data: JSON.stringify(data),
        success: function(response) {
          swAlert('Thông báo', response.message, 'success');

          setTimeout(() => {
            if (response.data && response.data.redirect_url) {
              window.location.href = '<?= base_url() ?>' + response.data.redirect_url;
            } else {
              window.location.href = '<?= base_url('/') ?>';
            }
          }, 1500);
        },
        error: function(xhr, status, error) {
          const errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Có lỗi xảy ra';
          swAlert('Thông báo', errorMessage, 'error');

          // Clear OTP inputs on error
          $('.otp-input').val('').removeClass('filled');
          $('.otp-input[data-index="0"]').focus();
        },
        complete: function() {
          // Re-enable submit button
          submitBtn.prop('disabled', false).val(originalValue);
        }
      });
    });

    // Resend OTP
    $('#resend-btn').click(function() {
      const $this = $(this);

      $this.prop('disabled', true).text('Đang gửi...');

      $.ajax({
        url: '<?= base_url('/register/otp/resend') ?>',
        dataType: 'json',
        contentType: 'application/json',
        type: 'POST',
        success: function(response) {
          swAlert('Thông báo', response.message, 'success');

          // Reset countdown
          timeLeft = 300;
          $('#countdown').show();
          $('#countdown-timer').text(timeLeft);
          startCountdown();

          // Clear OTP inputs
          $('.otp-input').val('').removeClass('filled');
          $('.otp-input[data-index="0"]').focus();
        },
        error: function(xhr, status, error) {
          const errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Có lỗi xảy ra';
          swAlert('Thông báo', errorMessage, 'error');
          $this.prop('disabled', false).text('Gửi lại mã xác thực');
        }
      });
    });

    // Focus first input on page load
    $('.otp-input[data-index="0"]').focus();
  });
</script>
<?php end_section() ?>
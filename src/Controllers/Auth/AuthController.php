<?php

namespace App\Controllers\Auth;

use App\Core\Cookies;
use App\Core\Hash;
use App\Core\RestApi;
use App\Core\Mail;
use App\Core\UserRole;
use App\Middlewares\AuthMiddleware;
use App\models\Account;
use Exception;

class AuthController
{
  public function showLogin()
  {
    $auth = new AuthMiddleware();
    $auth->redirectIfAuthenticated();

    render_view('auth/login', [], 'auth');
  }

  public function login()
  {
    RestApi::setHeaders();

    $body = RestApi::getBody();

    $email = $body['email'];
    $password = $body['password'];

    $user = Account::findByEmail($email);

    if (!isset($user)) {
      RestApi::responseError('Email không tồn tại');
    }

    if (!Hash::check($password, $user->password)) {
      RestApi::responseError('Mật khẩu không đúng');
    }

    // Lưu thông tin vào cookies
    unset($user->password);
    $cookie = new Cookies();
    $cookie->setAuth($user);

    RestApi::responseSuccess($user, 'Đăng nhập thành công');
  }

  public function logout()
  {
    RestApi::setHeaders();

    try {
      // Xóa cookies auth
      $cookie = new Cookies();
      $cookie->removeAuth();

      // Xóa session nếu có
      if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
      }

      RestApi::responseSuccess(null, 'Đăng xuất thành công');
    } catch (Exception $e) {
      RestApi::responseError('Có lỗi xảy ra khi đăng xuất');
    }
  }

  public function showRegister()
  {
    $auth = new AuthMiddleware();
    $auth->redirectIfAuthenticated();

    render_view('auth/register', [], 'auth');
  }

  public function register()
  {
    RestApi::setHeaders();

    try {
      $body = RestApi::getBody();

      // Validate input
      $name = trim($body['name'] ?? '');
      $email = trim($body['email'] ?? '');
      $phone = trim($body['phone'] ?? '');
      $password = $body['password'] ?? '';
      $confirmPassword = $body['confirm_password'] ?? '';

      // Kiểm tra các trường bắt buộc
      if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        RestApi::responseError('Vui lòng nhập đầy đủ thông tin');
      }

      // Kiểm tra định dạng email
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        RestApi::responseError('Email không hợp lệ');
      }

      // Kiểm tra mật khẩu
      if (strlen($password) < 6) {
        RestApi::responseError('Mật khẩu phải có ít nhất 6 ký tự');
      }

      if ($password !== $confirmPassword) {
        RestApi::responseError('Mật khẩu xác nhận không khớp');
      }

      // Kiểm tra số điện thoại (định dạng Việt Nam)
      if (!preg_match('/^(0[1|2|3|4|5|6|7|8|9])+([0-9]{8})$/', $phone)) {
        RestApi::responseError('Số điện thoại không hợp lệ');
      }

      // Kiểm tra email đã tồn tại
      $existingUserByEmail = Account::findByEmail($email);
      if ($existingUserByEmail) {
        RestApi::responseError('Email đã được sử dụng');
      }

      // Kiểm tra số điện thoại đã tồn tại
      $existingUserByPhone = Account::findByPhone($phone);
      if ($existingUserByPhone) {
        RestApi::responseError('Số điện thoại đã được sử dụng');
      }

      // Tạo OTP và thời gian hết hạn
      $otp = sprintf('%06d', mt_rand(100000, 999999));
      $otpExpiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

      // Lưu thông tin tạm thời vào session
      $_SESSION['register_temp'] = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'password' => Hash::make($password),
        'otp' => $otp,
        'otp_expiry' => $otpExpiry,
        'created_at' => date('Y-m-d H:i:s')
      ];

      // Gửi email OTP
      $mail = new Mail();
      $subject = 'Mã xác thực đăng ký tài khoản Pawspa';
      $message = $this->getOtpEmailTemplate($name, $otp);

      $mailSent = $mail->send($email, $subject, $message);

      if (!$mailSent) {
        RestApi::responseError('Không thể gửi email xác thực. Vui lòng thử lại.');
      }

      RestApi::responseSuccess([
        'redirect_url' => '/register/otp'
      ], 'Mã xác thực đã được gửi đến email của bạn');
    } catch (Exception $e) {
      RestApi::responseError('Có lỗi xảy ra: ' . $e->getMessage());
    }
  }

  public function showRegisterOtp()
  {
    // Kiểm tra có thông tin đăng ký tạm thời không
    if (!isset($_SESSION['register_temp'])) {
      header('Location: /register');
      exit;
    }

    // Kiểm tra OTP có hết hạn không
    $registerTemp = $_SESSION['register_temp'];
    if (strtotime($registerTemp['otp_expiry']) < time()) {
      unset($_SESSION['register_temp']);
      header('Location: /register?error=otp_expired');
      exit;
    }

    render_view('auth/register_otp', [
      'email' => $registerTemp['email']
    ], 'auth');
  }

  public function verifyOtp()
  {
    RestApi::setHeaders();

    try {
      if (!isset($_SESSION['register_temp'])) {
        RestApi::responseError('Phiên đăng ký đã hết hạn');
      }

      $body = RestApi::getBody();
      $inputOtp = $body['otp'] ?? '';

      if (empty($inputOtp)) {
        RestApi::responseError('Vui lòng nhập mã OTP');
      }

      $registerTemp = $_SESSION['register_temp'];

      // Kiểm tra OTP có hết hạn không
      if (strtotime($registerTemp['otp_expiry']) < time()) {
        unset($_SESSION['register_temp']);
        RestApi::responseError('Mã OTP đã hết hạn');
      }

      // Kiểm tra OTP có đúng không
      if ($inputOtp !== $registerTemp['otp']) {
        RestApi::responseError('Mã OTP không đúng');
      }

      // Tạo tài khoản mới
      $newUser = Account::create([
        'name' => $registerTemp['name'],
        'email' => $registerTemp['email'],
        'phone' => $registerTemp['phone'],
        'password' => $registerTemp['password'],
        'role' => UserRole::CUSTOMER,
        'verify_email_at' => date('Y-m-d H:i:s'),
        'rating' => 0
      ]);

      // Xóa thông tin tạm thời
      unset($_SESSION['register_temp']);

      // Tự động đăng nhập
      unset($newUser->password);
      $cookie = new Cookies();
      $cookie->setAuth($newUser);

      RestApi::responseSuccess([
        'user' => $newUser,
        'redirect_url' => '/'
      ], 'Đăng ký thành công');
    } catch (Exception $e) {
      RestApi::responseError('Có lỗi xảy ra: ' . $e->getMessage());
    }
  }

  public function resendOtp()
  {
    RestApi::setHeaders();

    try {

      if (!isset($_SESSION['register_temp'])) {
        RestApi::responseError('Phiên đăng ký đã hết hạn');
      }

      $registerTemp = $_SESSION['register_temp'];

      // Tạo OTP mới
      $newOtp = sprintf('%06d', mt_rand(100000, 999999));
      $newOtpExpiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

      // Cập nhật session
      $_SESSION['register_temp']['otp'] = $newOtp;
      $_SESSION['register_temp']['otp_expiry'] = $newOtpExpiry;

      // Gửi email OTP mới
      $mail = new Mail();
      $subject = 'Mã xác thực đăng ký tài khoản Pawspa';
      $message = $this->getOtpEmailTemplate($registerTemp['name'], $newOtp);

      $mailSent = $mail->send($registerTemp['email'], $subject, $message);

      if (!$mailSent) {
        RestApi::responseError('Không thể gửi lại email xác thực. Vui lòng thử lại.');
      }

      RestApi::responseSuccess(null, 'Mã xác thực mới đã được gửi đến email của bạn');
    } catch (Exception $e) {
      RestApi::responseError('Có lỗi xảy ra: ' . $e->getMessage());
    }
  }

  public function showForgotPassword()
  {
    render_view('auth/forgot_password', [], 'auth');
  }

  public function forgotPassword()
  {
    RestApi::setHeaders();

    try {
      $body = RestApi::getBody();
      $email = trim($body['email'] ?? '');

      // Validate input
      if (empty($email)) {
        RestApi::responseError('Vui lòng nhập email');
      }

      // Kiểm tra định dạng email
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        RestApi::responseError('Email không hợp lệ');
      }

      // Kiểm tra email có tồn tại trong hệ thống không
      $user = Account::findByEmail($email);
      if (!$user) {
        RestApi::responseError('Email không tồn tại trong hệ thống');
      }

      // Tạo token reset password
      $resetToken = bin2hex(random_bytes(32));
      $resetTokenExpiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));

      // Cập nhật token vào database
      $user->updateResetPasswordToken($resetToken, $resetTokenExpiry);

      // Gửi email reset password
      $mail = new Mail();
      $subject = 'Khôi phục mật khẩu tài khoản Pawspa';
      $resetUrl = base_url("/reset-password?token={$resetToken}");
      $message = $this->getResetPasswordEmailTemplate($user->name, $resetUrl);

      $mailSent = $mail->send($email, $subject, $message);

      if (!$mailSent) {
        RestApi::responseError('Không thể gửi email khôi phục. Vui lòng thử lại.');
      }

      RestApi::responseSuccess(null, 'Liên kết khôi phục mật khẩu đã được gửi đến email của bạn');
    } catch (Exception $e) {
      RestApi::responseError('Có lỗi xảy ra: ' . $e->getMessage());
    }
  }

  public function showResetPassword()
  {
    $token = $_GET['token'] ?? '';

    if (empty($token)) {
      header('Location: /forgot-password?error=invalid_token');
      exit;
    }

    // Kiểm tra token có hợp lệ không
    $user = Account::findByResetToken($token);
    if (!$user) {
      render_view('auth/reset_password_error', [
        'error' => 'Liên kết khôi phục không hợp lệ hoặc đã hết hạn'
      ], 'auth');
      return;
    }

    // Kiểm tra token có hết hạn không
    if (strtotime($user->reset_password_at) < time()) {
      render_view('auth/reset_password_error', [
        'error' => 'Liên kết khôi phục đã hết hạn'
      ], 'auth');
      return;
    }

    render_view('auth/reset_password', [
      'token' => $token
    ], 'auth');
  }

  public function resetPassword()
  {
    RestApi::setHeaders();

    try {
      $body = RestApi::getBody();
      $token = trim($body['token'] ?? '');
      $password = $body['password'] ?? '';
      $confirmPassword = $body['confirm_password'] ?? '';

      // Validate input
      if (empty($token) || empty($password) || empty($confirmPassword)) {
        RestApi::responseError('Vui lòng nhập đầy đủ thông tin');
      }

      // Kiểm tra mật khẩu
      if (strlen($password) < 6) {
        RestApi::responseError('Mật khẩu phải có ít nhất 6 ký tự');
      }

      if ($password !== $confirmPassword) {
        RestApi::responseError('Mật khẩu xác nhận không khớp');
      }

      // Kiểm tra token
      $user = Account::findByResetToken($token);
      if (!$user) {
        RestApi::responseError('Liên kết khôi phục không hợp lệ');
      }

      // Kiểm tra token có hết hạn không
      if (strtotime($user->reset_password_at) < time()) {
        RestApi::responseError('Liên kết khôi phục đã hết hạn');
      }

      // Cập nhật mật khẩu mới
      $hashedPassword = Hash::make($password);
      $user->updatePassword($hashedPassword);

      // Xóa token reset password
      $user->clearResetPasswordToken();

      RestApi::responseSuccess([
        'redirect_url' => '/login'
      ], 'Mật khẩu đã được cập nhật thành công');
    } catch (Exception $e) {
      RestApi::responseError('Có lỗi xảy ra: ' . $e->getMessage());
    }
  }

  private function getResetPasswordEmailTemplate($name, $resetUrl)
  {
    return "
      <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background-color: #f8f9fa; padding: 20px; text-align: center;'>
          <h2 style='color: #333; margin: 0;'>Pawspa</h2>
          <p style='color: #666; margin: 5px 0 0 0;'>Dịch vụ chăm sóc thú cưng uy tín</p>
        </div>
        
        <div style='padding: 30px 20px; background-color: white;'>
          <h3 style='color: #333; margin-bottom: 20px;'>Xin chào {$name},</h3>
          
          <p style='color: #555; line-height: 1.6; margin-bottom: 20px;'>
            Chúng tôi nhận được yêu cầu khôi phục mật khẩu cho tài khoản của bạn tại Pawspa. 
            Để đặt lại mật khẩu, vui lòng nhấp vào nút bên dưới:
          </p>
          
          <div style='text-align: center; margin: 30px 0;'>
            <a href='{$resetUrl}' style='display: inline-block; background-color: #007bff; 
                                       color: white; padding: 15px 30px; border-radius: 8px; 
                                       text-decoration: none; font-weight: bold;'>
              Đặt lại mật khẩu
            </a>
          </div>
          
          <p style='color: #555; line-height: 1.6; margin-bottom: 20px;'>
            Liên kết này có hiệu lực trong <strong>30 phút</strong>. 
            Nếu bạn không thể nhấp vào nút, hãy sao chép và dán liên kết sau vào trình duyệt:
          </p>
          
          <p style='color: #007bff; word-break: break-all; background-color: #f8f9fa; 
                    padding: 10px; border-radius: 4px; margin-bottom: 20px;'>
            {$resetUrl}
          </p>
          
          <p style='color: #888; font-size: 14px; margin-top: 30px;'>
            Nếu bạn không yêu cầu khôi phục mật khẩu, vui lòng bỏ qua email này. 
            Mật khẩu của bạn sẽ không thay đổi.
          </p>
        </div>
        
        <div style='background-color: #f8f9fa; padding: 20px; text-align: center; 
                    border-top: 1px solid #dee2e6;'>
          <p style='color: #666; margin: 0; font-size: 14px;'>
            © 2024 Pawspa. Tất cả quyền được bảo lưu.
          </p>
        </div>
      </div>
      ";
  }

  private function getOtpEmailTemplate($name, $otp)
  {
    return "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
      <div style='background-color: #f8f9fa; padding: 20px; text-align: center;'>
        <h2 style='color: #333; margin: 0;'>Pawspa</h2>
        <p style='color: #666; margin: 5px 0 0 0;'>Dịch vụ chăm sóc thú cưng uy tín</p>
      </div>
      
      <div style='padding: 30px 20px; background-color: white;'>
        <h3 style='color: #333; margin-bottom: 20px;'>Xin chào {$name},</h3>
        
        <p style='color: #555; line-height: 1.6; margin-bottom: 20px;'>
          Cảm ơn bạn đã đăng ký tài khoản tại Pawspa. Để hoàn tất quá trình đăng ký, 
          vui lòng sử dụng mã xác thực dưới đây:
        </p>
        
        <div style='text-align: center; margin: 30px 0;'>
          <div style='display: inline-block; background-color: #007bff; color: white; 
                      padding: 15px 30px; border-radius: 8px; font-size: 24px; 
                      font-weight: bold; letter-spacing: 2px;'>
            {$otp}
          </div>
        </div>
        
        <p style='color: #555; line-height: 1.6; margin-bottom: 20px;'>
          Mã xác thực này có hiệu lực trong <strong>5 phút</strong>. 
          Vui lòng không chia sẻ mã này với bất kỳ ai.
        </p>
        
        <p style='color: #888; font-size: 14px; margin-top: 30px;'>
          Nếu bạn không thực hiện đăng ký này, vui lòng bỏ qua email này.
        </p>
      </div>
      
      <div style='background-color: #f8f9fa; padding: 20px; text-align: center; 
                  border-top: 1px solid #dee2e6;'>
        <p style='color: #666; margin: 0; font-size: 14px;'>
          © 2024 Pawspa. Tất cả quyền được bảo lưu.
        </p>
      </div>
    </div>
    ";
  }
}

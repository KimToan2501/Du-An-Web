<?php

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\Hash;
use App\Core\RestApi;
use App\Core\Uploader;
use App\models\Account;
use Exception;

class ProfileApiController
{
  /**
   * Cập nhật thông tin profile
   */
  public function updateProfile()
  {
    try {
      // Set headers for upload
      RestApi::setHeaders(true);

      // Kiểm tra user đã đăng nhập
      $auth = Auth::getInstance();
      $user = $auth->user();

      if (!$user) {
        RestApi::responseError('Unauthorized', 401);
      }

      // Validate input
      $errors = $this->validateProfileData($_POST);
      if (!empty($errors)) {
        RestApi::response(['success' => false, 'errors' => $errors], 400);
      }

      // Xử lý upload avatar nếu có
      $avatarPath = null;
      if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatarPath = $this->handleAvatarUpload($_FILES['avatar'], $user['user_id']);
        if (!$avatarPath) {
          RestApi::responseError('Không thể upload avatar', 400);
        }
      }

      // Cập nhật thông tin user
      $account = Account::find($user['user_id']);
      if (!$account) {
        RestApi::responseError('User not found', 404);
      }

      // Chuẩn bị dữ liệu cập nhật
      $updateData = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'phone' => trim($_POST['phone']) ?: null,
        'address' => trim($_POST['address']) ?: null,
        'updated_at' => date('Y-m-d H:i:s')
      ];

      if ($avatarPath) {
        $updateData['avatar_url'] = $avatarPath;
      }

      // Thực hiện cập nhật
      $result = Account::updateById($user['user_id'], $updateData);

      if ($result) {
        // Cập nhật lại session/cookie nếu cần
        $this->updateUserSession($updateData);

        RestApi::responseSuccess(
          ['user' => array_merge($user, $updateData)],
          'Cập nhật thông tin thành công'
        );
      } else {
        RestApi::responseError('Không thể cập nhật thông tin', 500);
      }
    } catch (Exception $e) {
      error_log($e->getMessage());
      RestApi::responseError('Có lỗi xảy ra', 500);
    }
  }

  /**
   * Đổi mật khẩu
   */
  public function changePassword()
  {
    try {
      // Set headers
      RestApi::setHeaders();

      // Kiểm tra user đã đăng nhập
      $auth = Auth::getInstance();
      $user = $auth->user();

      if (!$user) {
        RestApi::responseError('Unauthorized', 401);
      }

      // Get request body
      $requestData = RestApi::getBody();
      if (!$requestData) {
        $requestData = $_POST;
      }

      // Validate input
      $errors = $this->validatePasswordData($requestData);
      if (!empty($errors)) {
        RestApi::response(['success' => false, 'errors' => $errors], 400);
      }

      // Lấy thông tin user từ DB
      $account = Account::find($user['user_id']);
      if (!$account) {
        RestApi::responseError('User not found', 404);
      }

      // Kiểm tra mật khẩu hiện tại
      if (!Hash::check($requestData['currentPassword'], $account->password)) {
        RestApi::response([
          'success' => false,
          'errors' => ['currentPassword' => 'Mật khẩu hiện tại không đúng']
        ], 400);
      }

      // Cập nhật mật khẩu mới
      $hashedPassword = Hash::make($requestData['newPassword']);
      $result = $account->updatePassword($hashedPassword);

      if ($result) {
        RestApi::responseSuccess(
          null,
          'Đổi mật khẩu thành công'
        );
      } else {
        RestApi::responseError('Không thể đổi mật khẩu', 500);
      }
    } catch (Exception $e) {
      error_log($e->getMessage());
      RestApi::responseError('Có lỗi xảy ra', 500);
    }
  }

  /**
   * Lấy thông tin profile hiện tại
   */
  public function getProfile()
  {
    try {
      RestApi::setHeaders();

      // Kiểm tra user đã đăng nhập
      $auth = Auth::getInstance();
      $user = $auth->user();

      if (!$user) {
        RestApi::responseError('Unauthorized', 401);
      }

      // Lấy thông tin user từ DB
      $account = Account::find($user['user_id']);
      if (!$account) {
        RestApi::responseError('User not found', 404);
      }

      // Loại bỏ password khỏi response
      $profileData = [
        'user_id' => $account->user_id,
        'name' => $account->name,
        'email' => $account->email,
        'phone' => $account->phone,
        'address' => $account->address,
        'avatar_url' => $account->avatar_url,
        'created_at' => $account->created_at,
        'updated_at' => $account->updated_at
      ];

      RestApi::responseSuccess(
        ['profile' => $profileData],
        'Lấy thông tin profile thành công'
      );
    } catch (Exception $e) {
      error_log($e->getMessage());
      RestApi::responseError('Có lỗi xảy ra', 500);
    }
  }

  /**
   * Validate dữ liệu profile
   */
  private function validateProfileData($data)
  {
    $errors = [];

    // Validate name
    if (empty(trim($data['name']))) {
      $errors['name'] = 'Vui lòng nhập họ tên';
    } else if (strlen(trim($data['name'])) < 2) {
      $errors['name'] = 'Họ tên phải có ít nhất 2 ký tự';
    } else if (strlen(trim($data['name'])) > 50) {
      $errors['name'] = 'Họ tên không được vượt quá 50 ký tự';
    }

    // Validate email
    if (empty(trim($data['email']))) {
      $errors['email'] = 'Vui lòng nhập email';
    } else if (!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = 'Email không hợp lệ';
    } else {
      // Kiểm tra email đã tồn tại chưa (trừ email hiện tại)
      $auth = Auth::getInstance();
      $currentUser = $auth->user();
      $existingUser = Account::findByEmail(trim($data['email']));

      if ($existingUser && $existingUser->user_id != $currentUser['user_id']) {
        $errors['email'] = 'Email này đã được sử dụng';
      }
    }

    // Validate phone (optional)
    if (!empty(trim($data['phone']))) {
      if (!preg_match('/^[0-9]{10}$/', trim($data['phone']))) {
        $errors['phone'] = 'Số điện thoại phải có 10-11 chữ số';
      } else {
        // Kiểm tra phone đã tồn tại chưa (trừ phone hiện tại)
        $auth = Auth::getInstance();
        $currentUser = $auth->user();
        $existingUser = Account::findByPhone(trim($data['phone']));

        if ($existingUser && $existingUser->user_id != $currentUser['user_id']) {
          $errors['phone'] = 'Số điện thoại này đã được sử dụng';
        }
      }
    }

    // Validate address (optional)
    if (!empty(trim($data['address'])) && strlen(trim($data['address'])) > 255) {
      $errors['address'] = 'Địa chỉ không được vượt quá 255 ký tự';
    }

    return $errors;
  }

  /**
   * Validate dữ liệu đổi mật khẩu
   */
  private function validatePasswordData($data)
  {
    $errors = [];

    // Validate current password
    if (empty($data['currentPassword'])) {
      $errors['currentPassword'] = 'Vui lòng nhập mật khẩu hiện tại';
    }

    // Validate new password
    if (empty($data['newPassword'])) {
      $errors['newPassword'] = 'Vui lòng nhập mật khẩu mới';
    } else {
      $password = $data['newPassword'];

      // Kiểm tra độ dài
      if (strlen($password) < 8) {
        $errors['newPassword'] = 'Mật khẩu phải có ít nhất 8 ký tự';
      }
      // Kiểm tra có chữ hoa
      else if (!preg_match('/[A-Z]/', $password)) {
        $errors['newPassword'] = 'Mật khẩu phải có ít nhất một chữ cái viết hoa';
      }
      // Kiểm tra có chữ thường
      else if (!preg_match('/[a-z]/', $password)) {
        $errors['newPassword'] = 'Mật khẩu phải có ít nhất một chữ cái viết thường';
      }
      // Kiểm tra có số
      else if (!preg_match('/[0-9]/', $password)) {
        $errors['newPassword'] = 'Mật khẩu phải có ít nhất một chữ số';
      }
    }

    // Validate confirm password
    if (empty($data['confirmPassword'])) {
      $errors['confirmPassword'] = 'Vui lòng xác nhận mật khẩu mới';
    } else if ($data['newPassword'] !== $data['confirmPassword']) {
      $errors['confirmPassword'] = 'Mật khẩu xác nhận không khớp';
    }

    return $errors;
  }

  /**
   * Xử lý upload avatar
   */
  private function handleAvatarUpload($file)
  {
    return Uploader::uploadImage($file, 'public/uploads/avatars');
  }

  /**
   * Xóa avatar cũ
   */
  private function deleteOldAvatar($userId)
  {
    try {
      $account = Account::find($userId);
      if ($account && $account->avatar_url && file_exists($account->avatar_url)) {
        unlink($account->avatar_url);
      }
    } catch (Exception $e) {
      error_log('Delete old avatar error: ' . $e->getMessage());
    }
  }

  /**
   * Cập nhật session/cookie user
   */
  private function updateUserSession($userData)
  {
    try {
      $auth = Auth::getInstance();
      $currentUser = $auth->user();

      // Merge dữ liệu mới với dữ liệu hiện tại
      $updatedUser = array_merge($currentUser, $userData);

      // Cập nhật cookie (cần implement method này trong Cookies class)
      $cookies = new \App\Core\Cookies();
      $cookies->updateAuth($updatedUser);
    } catch (Exception $e) {
      error_log('Update session error: ' . $e->getMessage());
    }
  }
}

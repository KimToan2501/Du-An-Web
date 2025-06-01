<?php

namespace App\Controllers\Client;

use App\Core\Auth;

class ProfileController
{
  /**
   * Hiển thị trang profile
   */
  public function index()
  {
    // Kiểm tra user đã đăng nhập
    $auth = Auth::getInstance();

    if (!$auth->isLoggedIn()) {
      redirect('/login');
      return;
    }

    $data = [
      'title' => 'Thông tin cá nhân'
    ];

    render_view('client/user/profile', $data, 'client');
  }

  /**
   * Hiển thị trang chỉnh sửa profile
   */
  public function edit()
  {
    // Kiểm tra user đã đăng nhập
    $auth = Auth::getInstance();
    if (!$auth->isLoggedIn()) {
      redirect('/login');
      return;
    }

    $user = $auth->user();
    if (!$user) {
      redirect('/login');
      return;
    }

    $data = [
      'title' => 'Chỉnh sửa thông tin cá nhân'
    ];

    render_view('client/user/edit', $data, 'client');
  }
}

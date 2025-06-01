<?php

namespace App\controllers\client;

class UserController
{
  public function profile()
  {
    $data = [
      'title' => 'Thông tin cá nhân',
    ];

    render_view('client/user/profile', $data, 'client');
  }

  public function pet()
  {
    $data = [
      'title' => 'Thú cưng của tôi',
    ];

    render_view('client/user/pet', $data, 'client');
  }
}

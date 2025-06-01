<?php

namespace App\Middlewares;

use App\Core\Cookies;
use App\Core\UserRole;

class AuthMiddleware
{
  public static function isLoggedIn()
  {
    $cookies = new Cookies();
    $isLoggedIn = $cookies->getAuth();
    return isset($isLoggedIn) ? true : false;
  }

  public static function requireWebAuth()
  {
    if (!self::isLoggedIn()) {
      redirect(base_url('/login'));
    }
  }

  public function requiredAdmin()
  {
    if (!self::isLoggedIn()) {
      redirect(base_url('/login'));
    }

    $cookies = new Cookies();
    $user = $cookies->decodeAuth();

    if (!isset($user)) {
      redirect(base_url('/login'));
    }

    $role = $user['role'];
    if ($role != UserRole::ADMIN) {
      show_403('Bạn không có quyền truy cập trang này');
    }

    return true;
  }

  public static function unAccessCustomer()
  {
    if (!self::isLoggedIn()) {
      redirect(base_url('/login'));
    }

    $cookies = new Cookies();
    $user = $cookies->decodeAuth();
    if (!isset($user)) {
      redirect(base_url('/login'));
    }

    $role = $user['role'];

    if ($role == UserRole::CUSTOMER) {
      show_403('Bạn không có quyền truy cập trang này');
    }

    return true;
  }

  public function requiredStaff()
  {
    if (!self::isLoggedIn()) {
      redirect(base_url('/login'));
    }

    $cookies = new Cookies();
    $user = $cookies->decodeAuth();
    if (!isset($user)) {
      redirect(base_url('/login'));
    }

    $role = $user['role'];
    if ($role != UserRole::STAFF) {
      show_403('Bạn không có quyền truy cập trang này');
    }

    return true;
  }

  public function redirectIfAuthenticated()
  {

    $cookies = new Cookies();
    $user = $cookies->decodeAuth();

    if (!isset($user)) {
      return true;
    }

    $role = $user['role'];

    $redirectPath = match ($role) {
      'admin' => Configs('defaultSiteAdmin'),
      'staff' => Configs('defaultSiteStaff'),
      'customer' => Configs('defaultSiteClient')
    };

    redirect(base_url($redirectPath));
  }
}

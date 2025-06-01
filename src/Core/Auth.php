<?php

namespace App\Core;

use App\Core\Cookies;
use App\Middlewares\AuthMiddleware;
use App\models\Account;

class Auth
{
  private static $instance = null;
  private $user = null;
  private $isLoggedIn = false;

  private function __construct()
  {
    $this->loadUser();
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function loadUser()
  {
    $cookies = new Cookies();
    $this->user = $cookies->decodeAuth();
    $this->isLoggedIn = AuthMiddleware::isLoggedIn();
  }

  public function user()
  {
    $user = $this->user;

    if (!$user) {
      return null;
    }

    $role = $user['role'];

    $redirectPath = match ($role) {
      'admin' => Configs('defaultSiteAdmin'),
      'staff' => Configs('defaultSiteStaff'),
      'customer' => Configs('defaultSiteClient')
    };

    $user['redirect_path'] = $redirectPath;

    return $user;
  }

  public function getPoints()
  {
    $user = $this->user();
    if (!$user) {
      return 0;
    }

    $userFound = Account::find($user['user_id']);

    return $userFound->points ?? 0;
  }

  public function isLoggedIn()
  {
    return $this->isLoggedIn;
  }

  public function name()
  {
    return isset($this->user['name']) ? $this->user['name'] : 'Guest';
  }

  public function email()
  {
    return isset($this->user['email']) ? $this->user['email'] : '';
  }

  public function avatar()
  {
    return isset($this->user['avatar_url']) && !empty($this->user['avatar_url'])
      ? base_url($this->user['avatar_url'])
      : base_url('cms/assets/images/avatar/animal-avatar-bear.svg');
  }

  public function role()
  {
    return isset($this->user['role']) ? ucfirst($this->user['role']) : 'Guest';
  }

  public function logout()
  {
    $cookies = new Cookies();
    $cookies->removeAuth();
    $this->user = null;
    $this->isLoggedIn = false;
  }
}

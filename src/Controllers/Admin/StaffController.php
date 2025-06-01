<?php

namespace App\Controllers\Admin;

use App\Core\RestApi;
use App\Core\Hash;
use App\Core\Uploader;
use App\Core\UserRole;
use App\Models\Account;

class StaffController
{
  public function index()
  {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $searching = isset($_GET['q']) ? (string) $_GET['q'] : '';
    $perPage = 10;
    $result = empty($searching) ? Account::paginateWhere([
      'role' => UserRole::STAFF,
    ], $page, $perPage) : Account::paginateWhere([
      'name' => [
        'operator' => 'LIKE',
        'value' => "%{$searching}%"
      ],
      'role' => UserRole::STAFF
    ], $page, $perPage);

    $data = [
      'title' => 'Quản lý nhân viên',
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý nhân viên'],
      ],
      'metadata' => $result['data'],
      'pagination' => [
        'current' => $result['current_page'],
        'last' => $result['last_page'],
        'total' => $result['total']
      ],
      'searching' => $searching
    ];

    render_view('admin/staff/index', $data, 'admin');
  }

  public function showAdd()
  {
    $data = [
      'title' => 'Thêm nhân viên',
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý nhân viên', 'url' => '/admin/staff'],
        ['text' => 'Thêm nhân viên'],
      ],
    ];

    render_view('admin/staff/add', $data, 'admin');
  }

  public function add()
  {
    try {
      RestApi::setHeaders();

      $name = $_POST['name'];
      $email = $_POST['email'];
      $password = $_POST['password'];
      $phone = $_POST['phone'] ?? null;
      $address = $_POST['address'] ?? null;

      $check = Account::findByEmail($email);

      if (isset($check)) {
        RestApi::responseError('Email đã tồn tại');
      }

      // Handle image upload
      $avatarPath = Uploader::uploadImage($_FILES['avatar'], 'public/uploads/staff/');

      Account::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'phone' => $phone,
        'address' => $address,
        'avatar_url' => $avatarPath,
        'role' => UserRole::STAFF,
        'verify_email_at' => date('Y-m-d H:i:s'),
      ]);

      RestApi::responseSuccess(true, 'Đã thêm nhân viên thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function showUpdate($id)
  {
    $staff = Account::findOneBy('user_id', $id);

    if (!isset($staff) || $staff->role !== UserRole::STAFF) {
      redirect(base_url('/404'));
    }

    $title = 'Cập nhật: ' . $staff->name;

    $data = [
      'title' => $title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý nhân viên', 'url' => '/admin/staff'],
        ['text' => $title],
      ],
      'metadata' => $staff,
    ];

    render_view('admin/staff/update', $data, 'admin');
  }

  public function edit($id)
  {
    try {
      RestApi::setHeaders();

      $staff = Account::findOneBy('user_id', $id);

      if (!isset($staff) || $staff->role !== UserRole::STAFF) {
        RestApi::responseError('Nhân viên không tồn tại');
      }

      $name = $_POST['name'];
      $email = $_POST['email'];
      $phone = $_POST['phone'] ?? null;
      $address = $_POST['address'] ?? null;

      $check = Account::findByEmail($email);

      if (isset($check) && $check->user_id != $id) {
        RestApi::responseError('Email đã tồn tại');
      }

      // Handle image upload
      $avatarPath = Uploader::uploadImage($_FILES['avatar'], 'public/uploads/staff/');

      Account::update($id, [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'avatar_url' => $avatarPath,
      ]);

      RestApi::responseSuccess(true, 'Đã cập nhật nhân viên thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function delete($id)
  {
    try {
      RestApi::setHeaders();

      $staff = Account::findOneBy('user_id', $id);

      if (!isset($staff) || $staff->role !== UserRole::STAFF) {
        RestApi::responseError('Nhân viên không tồn tại');
      }

      // Handle image upload
      Uploader::deleteFile($staff->avatar_url);

      Account::delete($id);

      RestApi::responseSuccess(true, 'Đã xoá nhân viên thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }
}

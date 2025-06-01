<?php

namespace App\Controllers\Admin;

use App\Core\RestApi;
use App\Core\Hash;
use App\Core\UserRole;
use App\Models\Account;

class CustomerController
{
  public function index()
  {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $searching = isset($_GET['q']) ? (string) $_GET['q'] : '';
    $perPage = 10;
    $result = empty($searching) ? Account::paginateWhere([
      'role' => UserRole::CUSTOMER,
    ], $page, $perPage) : Account::paginateWhere([
      'name' => [
        'operator' => 'LIKE',
        'value' => "%{$searching}%"
      ],
      'role' => UserRole::CUSTOMER
    ], $page, $perPage);

    $data = [
      'title' => 'Quản lý khách hàng',
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý khách hàng'],
      ],
      'metadata' => $result['data'],
      'pagination' => [
        'current' => $result['current_page'],
        'last' => $result['last_page'],
        'total' => $result['total']
      ],
      'searching' => $searching
    ];

    render_view('admin/customer/index', $data, 'admin');
  }

  public function showAdd()
  {
    $data = [
      'title' => 'Thêm khách hàng',
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý khách hàng', 'url' => '/admin/customer'],
        ['text' => 'Thêm khách hàng'],
      ],
    ];

    render_view('admin/customer/add', $data, 'admin');
  }

  public function add()
  {
    try {
      RestApi::setHeaders();

      $body = RestApi::getBody();

      $name = $body['name'];
      $email = $body['email'];
      $password = $body['password'];
      $phone = $body['phone'] ?? null;
      $address = $body['address'] ?? null;

      $check = Account::findByEmail($email);

      if (isset($check)) {
        RestApi::responseError('Email đã tồn tại');
      }

      Account::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'phone' => $phone,
        'address' => $address,
        'role' => UserRole::CUSTOMER,
        'verify_email_at' => date('Y-m-d H:i:s'),
      ]);

      RestApi::responseSuccess(true, 'Đã thêm khách hàng thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function showUpdate($id)
  {
    $customer = Account::findOneBy('user_id', $id);

    if (!isset($customer) || $customer->role !== UserRole::CUSTOMER) {
      redirect(base_url('/404'));
    }

    $title = 'Cập nhật: ' . $customer->name;

    $data = [
      'title' => $title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý khách hàng', 'url' => '/admin/customer'],
        ['text' => $title],
      ],
      'metadata' => $customer,
    ];

    render_view('admin/customer/update', $data, 'admin');
  }

  public function edit($id)
  {
    try {
      RestApi::setHeaders();

      $customer = Account::findOneBy('user_id', $id);

      if (!isset($customer) || $customer->role !== UserRole::CUSTOMER) {
        RestApi::responseError('khách hàng không tồn tại');
      }

      $body = RestApi::getBody();

      $name = $body['name'];
      $email = $body['email'];
      $phone = $body['phone'] ?? null;
      $address = $body['address'] ?? null;

      $check = Account::findByEmail($email);

      if (isset($check) && $check->user_id != $id) {
        RestApi::responseError('Email đã tồn tại');
      }

      Account::update($id, [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
      ]);

      RestApi::responseSuccess(true, 'Đã cập nhật khách hàng thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function delete($id)
  {
    try {
      RestApi::setHeaders();

      $customer = Account::findOneBy('user_id', $id);

      if (!isset($customer) || $customer->role !== UserRole::CUSTOMER) {
        RestApi::responseError('khách hàng không tồn tại');
      }

      Account::delete($id);

      RestApi::responseSuccess(true, 'Đã xoá khách hàng thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }
}

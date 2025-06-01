<?php

namespace App\Controllers\Admin;

use App\Core\RestApi;
use App\Models\Discount;

class discountController
{
  public function index()
  {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $searching = isset($_GET['q']) ? (string) $_GET['q'] : '';
    $perPage = 10;
    $result = empty($searching) ? Discount::paginate($page, $perPage) : Discount::paginateWhere([
      'code' => [
        'operator' => 'LIKE',
        'value' => "%{$searching}%"
      ],
    ], $page, $perPage);

    $data = [
      'title' => 'Quản lý khuyến mãi',
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý khuyến mãi'],
      ],
      'metadata' => $result['data'],
      'pagination' => [
        'current' => $result['current_page'],
        'last' => $result['last_page'],
        'total' => $result['total']
      ],
      'searching' => $searching
    ];

    render_view('admin/discount/index', $data, 'admin');
  }

  public function showAdd()
  {
    $data = [
      'title' => 'Thêm khuyến mãi',
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý khuyến mãi', 'url' => '/admin/discount'],
        ['text' => 'Thêm khuyến mãi'],
      ],
    ];

    render_view('admin/discount/add', $data, 'admin');
  }

  public function add()
  {
    try {
      RestApi::setHeaders();

      $body = RestApi::getBody();

      $name = $body['name'];
      $code = $body['code'];
      $start_date = $body['start_date'];
      $end_date = $body['end_date'];
      $percent = $body['percent'];

      $check = Discount::findByCode($code);

      if (isset($check)) {
        RestApi::responseError('Mã khuyến mãi đã tồn tại');
      }

      Discount::create([
        'name' => $name,
        'code' => $code,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'percent' => $percent,
      ]);

      RestApi::responseSuccess(true, 'Đã thêm khuyến mãi thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function showUpdate($id)
  {
    $discount = Discount::findOneBy('discount_id', $id);

    if (!isset($discount)) {
      redirect(base_url('/404'));
    }

    $title = 'Cập nhật: ' . $discount->name;

    $data = [
      'title' => $title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý khuyến mãi', 'url' => '/admin/discount'],
        ['text' => $title],
      ],
      'metadata' => $discount,
    ];

    render_view('admin/discount/update', $data, 'admin');
  }

  public function edit($id)
  {
    try {
      RestApi::setHeaders();

      $discount = Discount::findOneBy('discount_id', $id);

      if (!isset($discount)) {
        RestApi::responseError('khuyến mãi không tồn tại');
      }

      $body = RestApi::getBody();

      $name = $body['name'];
      $start_date = $body['start_date'];
      $end_date = $body['end_date'];
      $percent = $body['percent'];

      Discount::update($id, [
        'name' => $name,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'percent' => $percent,
      ]);

      RestApi::responseSuccess(true, 'Đã cập nhật khuyến mãi thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function delete($id)
  {
    try {
      RestApi::setHeaders();

      $discount = Discount::findOneBy('discount_id', $id);

      if (!isset($discount)) {
        RestApi::responseError('khuyến mãi không tồn tại');
      }

      Discount::delete($id);

      RestApi::responseSuccess(true, 'Đã xoá khuyến mãi thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }
}

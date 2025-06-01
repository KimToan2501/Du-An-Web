<?php

namespace App\Controllers\Admin;

use App\Core\RestApi;
use App\Models\ServiceType;

class ServiceTypeController
{
  public function index()
  {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $searching = isset($_GET['q']) ? (string) $_GET['q'] : '';
    $perPage = 10;
    $result = empty($searching) ? ServiceType::paginate($page, $perPage) : ServiceType::paginateWhere([
      'name' => [
        'operator' => 'LIKE',
        'value' => "%{$searching}%"
      ]
    ], $page, $perPage);

    $data = [
      'title' => 'Quản lý loại dịch vụ',
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý loại dịch vụ'],
      ],
      'metadata' => $result['data'],
      'pagination' => [
        'current' => $result['current_page'],
        'last' => $result['last_page'],
        'total' => $result['total']
      ],
      'searching' => $searching
    ];

    render_view('admin/service-type/index', $data, 'admin');
  }

  public function showAdd()
  {
    $data = [
      'title' => 'Thêm loại dịch vụ',
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý loại dịch vụ', 'url' => '/admin/service-type'],
        ['text' => 'Thêm loại dịch vụ'],
      ],
    ];

    render_view('admin/service-type/add', $data, 'admin');  // Đường dẫn của file
  }

  public function add()
  {
    try {
      RestApi::setHeaders();

      $body = RestApi::getBody();

      $name = $body['name'];
      $description = $body['description'] ?? null;

      $check = ServiceType::findByName($name);

      if (isset($check)) {
        RestApi::responseError('Tên loại dịch vụ đã tồn tại');
      }

      ServiceType::create([
        'name' => $name,
        'description' => $description
      ]);

      RestApi::responseSuccess(true, 'Đã thêm loại dịch vụ thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }


  public function showEdit($id)
  {
    $serviceType = ServiceType::findOneBy('service_type_id', $id);

    if (!isset($serviceType)) {
      redirect(base_url('/404'));
    }

    $title = 'Cập nhật: ' . $serviceType->name;

    $data = [
      'title' => $title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý loại dịch vụ', 'url' => '/admin/service-type'],
        ['text' => $title],
      ],
      'metadata' => $serviceType
    ];

    render_view('admin/service-type/edit', $data, 'admin');   // Đường dẫn của file
  }

  public function edit($id)
  {
    try {
      RestApi::setHeaders();

      $serviceType = ServiceType::findOneBy('service_type_id', $id);

      if (!isset($serviceType)) {
        RestApi::responseError('Tên loại dịch vụ không tồn tại');
      }

      $body = RestApi::getBody();

      $name = $body['name'];
      $description = $body['description'] ?? null;

      $check = ServiceType::findByName($name);

      if (isset($check) && $check->service_type_id != $id) {
        RestApi::responseError('Tên loại dịch vụ đã tồn tại');
      }

      ServiceType::update($id, [
        'name' => $name,
        'description' => $description
      ]);

      RestApi::responseSuccess(true, 'Đã cập nhật loại dịch vụ thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function delete($id)
  {
    try {
      RestApi::setHeaders();

      $serviceType = ServiceType::findOneBy('service_type_id', $id);

      if (!isset($serviceType)) {
        RestApi::responseError('Tên loại dịch vụ không tồn tại');
      }

      ServiceType::delete($id);

      RestApi::responseSuccess(true, 'Đã xoá loại dịch vụ thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }
}

<?php

namespace App\Controllers\Admin;

use App\Core\RestApi;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\ServiceImage;

class ServiceController
{
  public function index()
  {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $searching = isset($_GET['q']) ? (string) $_GET['q'] : '';
    $perPage = 10;
    $result = empty($searching) ? Service::paginate($page, $perPage) : Service::paginateWhere([
      'name' => [
        'operator' => 'LIKE',
        'value' => "%{$searching}%"
      ]
    ], $page, $perPage);

    $data = [
      'title' => 'Quản lý dịch vụ',
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý dịch vụ'],
      ],
      'metadata' => $result['data'],
      'pagination' => [
        'current' => $result['current_page'],
        'last' => $result['last_page'],
        'total' => $result['total']
      ],
      'searching' => $searching
    ];

    render_view('admin/service/index', $data, 'admin');
  }

  public function showAdd()
  {
    $serviceTypes = ServiceType::all();

    $data = [
      'title' => 'Thêm dịch vụ',
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý dịch vụ', 'url' => '/admin/service'],
        ['text' => 'Thêm dịch vụ'],
      ],
      'serviceTypes' => $serviceTypes
    ];

    render_view('admin/service/add', $data, 'admin');
  }

  public function add()
  {
    try {
      RestApi::setHeaders();

      // Get form data
      $name = $_POST['name'] ?? '';
      $price = $_POST['price'] ?? 0;
      $service_type_id = $_POST['service_type_id'] ?? '';
      $discount_percent = $_POST['discount_percent'] ?? 0;
      $duration = $_POST['duration'] ?? 0;
      $description = $_POST['description'] ?? '';

      // Validate required fields
      if (empty($name) || empty($service_type_id) || empty($price) || empty($duration)) {
        RestApi::responseError('Vui lòng nhập đầy đủ thông tin bắt buộc');
      }

      // Check if service name already exists
      $check = Service::findByName($name);
      if (isset($check)) {
        RestApi::responseError('Tên dịch vụ đã tồn tại');
      }

      // Create service
      $serviceId = Service::create([
        'name' => $name,
        'price' => $price,
        'service_type_id' => $service_type_id,
        'duration' => $duration,
        'discount_percent' => $discount_percent,
        'description' => $description,
      ]);

      // Handle image uploads
      if (isset($_FILES['service_images']) && !empty($_FILES['service_images']['name'][0])) {
        $uploadedImages = $this->handleImageUploads($_FILES['service_images'], $serviceId->service_id);

        if (!$uploadedImages['success']) {
          // If image upload fails, we might want to delete the service or continue without images
          // For now, we'll continue but log the error
          error_log('Image upload failed: ' . $uploadedImages['message']);
        }
      }

      RestApi::responseSuccess(true, 'Đã thêm dịch vụ thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  private function handleImageUploads($files, $serviceId)
  {
    try {
      $uploadDir = 'public/uploads/services/';

      // Create upload directory if it doesn't exist
      if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }

      $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
      $maxFileSize = 5 * 1024 * 1024; // 5MB
      $uploadedCount = 0;

      // Handle multiple file upload
      $fileCount = count($files['name']);

      for ($i = 0; $i < $fileCount; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
          $tmpName = $files['tmp_name'][$i];
          $originalName = $files['name'][$i];
          $fileSize = $files['size'][$i];
          $fileType = $files['type'][$i];

          // Validate file type
          if (!in_array($fileType, $allowedTypes)) {
            continue; // Skip invalid file types
          }

          // Validate file size
          if ($fileSize > $maxFileSize) {
            continue; // Skip files that are too large
          }

          // Generate unique filename
          $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
          $fileName = 'service_' . $serviceId . '_' . time() . '_' . $i . '.' . $fileExtension;
          $filePath = $uploadDir . $fileName;

          // Move uploaded file
          if (move_uploaded_file($tmpName, $filePath)) {
            // Save to database
            ServiceImage::create([
              'service_id' => $serviceId,
              'image_url' => str_replace('public', '', $filePath)
            ]);
            $uploadedCount++;
          }
        }
      }

      return [
        'success' => true,
        'message' => "Đã upload {$uploadedCount} ảnh thành công",
        'uploaded_count' => $uploadedCount
      ];
    } catch (\Exception $e) {
      return [
        'success' => false,
        'message' => 'Lỗi khi upload ảnh: ' . $e->getMessage()
      ];
    }
  }

  public function showUpdate($id)
  {
    $service = Service::findOneBy('service_id', $id);
    $serviceTypes = ServiceType::all();

    if (!isset($service)) {
      redirect(base_url('/404'));
    }

    // Get service images
    $serviceImages = ServiceImage::findBy('service_id', $id);

    $title = 'Cập nhật: ' . $service->name;

    $data = [
      'title' => $title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý dịch vụ', 'url' => '/admin/service'],
        ['text' => $title],
      ],
      'metadata' => $service,
      'serviceTypes' => $serviceTypes,
      'serviceImages' => $serviceImages
    ];

    render_view('admin/service/update', $data, 'admin');
  }

  public function edit($id)
  {
    try {
      RestApi::setHeaders();

      $service = Service::findOneBy('service_id', $id);

      if (!isset($service)) {
        RestApi::responseError('Dịch vụ không tồn tại');
      }

      // Get form data
      $name = $_POST['name'] ?? '';
      $price = $_POST['price'] ?? 0;
      $service_type_id = $_POST['service_type_id'] ?? '';
      $discount_percent = $_POST['discount_percent'] ?? 0;
      $duration = $_POST['duration'] ?? 0;
      $description = $_POST['description'] ?? '';

      // Check if service name already exists (excluding current service)
      $check = Service::findByName($name);
      if (isset($check) && $check->service_id != $id) {
        RestApi::responseError('Tên dịch vụ đã tồn tại');
      }

      // Update service
      Service::update($id, [
        'name' => $name,
        'price' => $price,
        'service_type_id' => $service_type_id,
        'duration' => $duration,
        'discount_percent' => $discount_percent,
        'description' => $description,
      ]);

      // Handle new image uploads
      if (isset($_FILES['service_images']) && !empty($_FILES['service_images']['name'][0])) {
        $this->handleImageUploads($_FILES['service_images'], $id);
      }

      RestApi::responseSuccess(true, 'Đã cập nhật dịch vụ thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function deleteImage($imageId)
  {
    try {
      RestApi::setHeaders();

      $serviceImage = ServiceImage::findOneBy('image_id', $imageId);

      if (!isset($serviceImage)) {
        RestApi::responseError('Ảnh không tồn tại');
      }

      // Delete physical file
      if (file_exists($serviceImage->image_url)) {
        unlink($serviceImage->image_url);
      }

      // Delete from database
      ServiceImage::delete($imageId);

      RestApi::responseSuccess(true, 'Đã xóa ảnh thành công', 200);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function delete($id)
  {
    try {
      RestApi::setHeaders();

      $service = Service::findOneBy('service_id', $id);

      if (!isset($service)) {
        RestApi::responseError('Dịch vụ không tồn tại');
      }

      // Delete associated images
      $serviceImages = ServiceImage::findBy('service_id', $id);
      foreach ($serviceImages as $image) {
        // Delete physical file
        if (file_exists($image->image_url)) {
          unlink($image->image_url);
        }
        // Delete from database
        ServiceImage::delete($image->image_id);
      }

      // Delete service
      Service::delete($id);

      RestApi::responseSuccess(true, 'Đã xoá dịch vụ thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }
}

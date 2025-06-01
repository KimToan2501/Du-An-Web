<?php

namespace App\Controllers\Admin;

use App\Core\RestApi;
use App\Models\Blog;

class BlogController
{
  public function index()
  {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $searching = isset($_GET['q']) ? (string) $_GET['q'] : '';
    $status = isset($_GET['status']) ? (string) $_GET['status'] : '';
    $perPage = 10;

    // Xây dựng điều kiện tìm kiếm
    $conditions = [];

    if (!empty($searching)) {
      $conditions['title'] = [
        'operator' => 'LIKE',
        'value' => "%{$searching}%"
      ];
    }

    if (!empty($status)) {
      $conditions['status'] = $status;
    }

    $result = empty($conditions) ?
      Blog::paginate($page, $perPage, 'created_at', 'DESC') :
      Blog::paginateWhere($conditions, $page, $perPage, 'AND', 'created_at', 'DESC');

    $data = [
      'title' => 'Quản lý Blog',
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý Blog'],
      ],
      'metadata' => $result['data'],
      'pagination' => [
        'current' => $result['current_page'],
        'last' => $result['last_page'],
        'total' => $result['total']
      ],
      'searching' => $searching,
      'status' => $status
    ];

    render_view('admin/blog/index', $data, 'admin');
  }

  public function showAdd()
  {
    $data = [
      'title' => 'Thêm Blog',
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý Blog', 'url' => '/admin/blog'],
        ['text' => 'Thêm Blog'],
      ]
    ];

    render_view('admin/blog/add', $data, 'admin');
  }

  public function add()
  {
    try {
      RestApi::setHeaders();

      // Get form data
      $title = $_POST['title'] ?? '';
      $content = $_POST['content'] ?? '';
      $excerpt = $_POST['excerpt'] ?? '';
      $status = $_POST['status'] ?? 'draft';
      $meta_title = $_POST['meta_title'] ?? '';
      $meta_description = $_POST['meta_description'] ?? '';

      // Validate required fields
      if (empty($title) || empty($content)) {
        RestApi::responseError('Vui lòng nhập đầy đủ tiêu đề và nội dung');
      }

      // Create unique slug
      $slug = Blog::createUniqueSlug($title);

      // Handle featured image upload
      $featured_image = '';
      if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = $this->handleImageUpload($_FILES['featured_image']);
        if ($uploadResult['success']) {
          $featured_image = $uploadResult['file_path'];
        } else {
          RestApi::responseError('Lỗi upload ảnh: ' . $uploadResult['message']);
        }
      }

      // Create blog
      $blog = Blog::create([
        'title' => $title,
        'slug' => $slug,
        'content' => $content,
        'excerpt' => $excerpt,
        'featured_image' => $featured_image,
        'status' => $status,
        'meta_title' => $meta_title,
        'meta_description' => $meta_description,
      ]);

      if (!$blog) {
        RestApi::responseError('Có lỗi xảy ra khi tạo blog');
      }

      RestApi::responseSuccess(true, 'Đã thêm blog thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function showUpdate($id)
  {
    $blog = Blog::findOneBy('blog_id', $id);

    if (!isset($blog)) {
      redirect(base_url('/404'));
    }

    $title = 'Cập nhật: ' . $blog->title;

    $data = [
      'title' => $title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý Blog', 'url' => '/admin/blog'],
        ['text' => $title],
      ],
      'metadata' => $blog
    ];

    render_view('admin/blog/update', $data, 'admin');
  }

  public function edit($id)
  {
    try {
      RestApi::setHeaders();

      $blog = Blog::findOneBy('blog_id', $id);

      if (!isset($blog)) {
        RestApi::responseError('Blog không tồn tại');
      }

      // Get form data
      $title = $_POST['title'] ?? '';
      $content = $_POST['content'] ?? '';
      $excerpt = $_POST['excerpt'] ?? '';
      $status = $_POST['status'] ?? 'draft';
      $meta_title = $_POST['meta_title'] ?? '';
      $meta_description = $_POST['meta_description'] ?? '';

      // Validate required fields
      if (empty($title) || empty($content)) {
        RestApi::responseError('Vui lòng nhập đầy đủ tiêu đề và nội dung');
      }

      // Create unique slug if title changed
      $slug = $blog->slug;
      if ($title !== $blog->title) {
        $slug = Blog::createUniqueSlug($title, $id);
      }

      // Handle featured image upload
      $featured_image = $blog->featured_image;
      if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        // Delete old image if exists
        if (!empty($blog->featured_image) && file_exists('public' . $blog->featured_image)) {
          unlink('public' . $blog->featured_image);
        }

        $uploadResult = $this->handleImageUpload($_FILES['featured_image']);
        if ($uploadResult['success']) {
          $featured_image = $uploadResult['file_path'];
        } else {
          RestApi::responseError('Lỗi upload ảnh: ' . $uploadResult['message']);
        }
      }

      // Update blog
      $updated = Blog::update($id, [
        'title' => $title,
        'slug' => $slug,
        'content' => $content,
        'excerpt' => $excerpt,
        'featured_image' => $featured_image,
        'status' => $status,
        'meta_title' => $meta_title,
        'meta_description' => $meta_description,
      ]);

      if (!$updated) {
        RestApi::responseError('Có lỗi xảy ra khi cập nhật blog');
      }

      RestApi::responseSuccess(true, 'Đã cập nhật blog thành công', 200);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function delete($id)
  {
    try {
      RestApi::setHeaders();

      $blog = Blog::findOneBy('blog_id', $id);

      if (!isset($blog)) {
        RestApi::responseError('Blog không tồn tại');
      }

      // Delete featured image if exists
      if (!empty($blog->featured_image) && file_exists('public' . $blog->featured_image)) {
        unlink('public' . $blog->featured_image);
      }

      // Delete blog
      $deleted = Blog::delete($id);

      if (!$deleted) {
        RestApi::responseError('Có lỗi xảy ra khi xóa blog');
      }

      RestApi::responseSuccess(true, 'Đã xóa blog thành công', 200);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  private function handleImageUpload($file)
  {
    try {
      $uploadDir = 'public/uploads/blogs/';

      // Create upload directory if it doesn't exist
      if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }

      $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
      $maxFileSize = 5 * 1024 * 1024; // 5MB

      $tmpName = $file['tmp_name'];
      $originalName = $file['name'];
      $fileSize = $file['size'];
      $fileType = $file['type'];

      // Validate file type
      if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Định dạng file không được hỗ trợ'];
      }

      // Validate file size
      if ($fileSize > $maxFileSize) {
        return ['success' => false, 'message' => 'File quá lớn (tối đa 5MB)'];
      }

      // Generate unique filename
      $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
      $fileName = 'blog_' . time() . '_' . uniqid() . '.' . $fileExtension;
      $filePath = $uploadDir . $fileName;

      // Move uploaded file
      if (move_uploaded_file($tmpName, $filePath)) {
        return [
          'success' => true,
          'file_path' => str_replace('public', '', $filePath),
          'message' => 'Upload thành công'
        ];
      } else {
        return ['success' => false, 'message' => 'Không thể di chuyển file'];
      }
    } catch (\Exception $e) {
      return ['success' => false, 'message' => $e->getMessage()];
    }
  }

  public function uploadImage()
  {
    try {
      RestApi::setHeaders();

      if (!isset($_FILES['file'])) {
        RestApi::responseError('Không có file được tải lên');
      }

      $uploadResult = $this->handleImageUpload($_FILES['file']);

      if ($uploadResult['success']) {
        RestApi::responseSuccess([
          'success' => true,
          'url' => base_url($uploadResult['file_path'])
        ], 'Upload thành công');
      } else {
        RestApi::responseError($uploadResult['message']);
      }
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function show($id)
  {
    $blog = Blog::findOneBy('blog_id', $id);

    if (!isset($blog)) {
      redirect(base_url('/404'));
    }

    $data = [
      'title' => $blog->title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý Blog', 'url' => '/admin/blog'],
        ['text' => $blog->title],
      ],
      'metadata' => $blog
    ];

    render_view('admin/blog/details', $data, 'admin');
  }
}

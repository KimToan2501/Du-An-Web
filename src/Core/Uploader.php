<?php

namespace App\Core;

class Uploader
{
  public static function uploadImage($file, $uploadDir = 'public/uploads/')
  {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
      return null;
    }

    $targetDir = $uploadDir;
    if (!is_dir($targetDir)) {
      mkdir($targetDir, 0777, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $targetPath = $targetDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
      return str_replace('public', '', $targetPath);
    }

    return null;
  }

  public static function deleteFile($filePath)
  {
    if (empty($filePath) || !is_string($filePath) || !isset($filePath)) {
      return false;
    }

    // Construct the full path to the file
    $fullPath = 'public' . $filePath;

    // Check if the file exists and delete it
    if (file_exists($fullPath) && is_file($fullPath)) {
      return unlink($fullPath);
    }

    return false;
  }

  public static function checkUploadError($file)
  {
    if (!isset($file) || !is_array($file)) {
      return 'File không hợp lệ';
    }

    switch ($file['error']) {
      case UPLOAD_ERR_OK:
        return null; // Không có lỗi
      case UPLOAD_ERR_INI_SIZE:
      case UPLOAD_ERR_FORM_SIZE:
        return 'File quá lớn. Kích thước tối đa: ' . ini_get('upload_max_filesize');
      case UPLOAD_ERR_PARTIAL:
        return 'File chỉ được upload một phần';
      case UPLOAD_ERR_NO_FILE:
        return 'Không có file được chọn';
      case UPLOAD_ERR_NO_TMP_DIR:
        return 'Thiếu thư mục tạm';
      case UPLOAD_ERR_CANT_WRITE:
        return 'Không thể ghi file';
      case UPLOAD_ERR_EXTENSION:
        return 'Upload bị chặn bởi extension';
      default:
        return 'Lỗi không xác định';
    }
  }
}

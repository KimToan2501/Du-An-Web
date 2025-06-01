<?php

namespace App\Controllers\Error;

class ErrorController
{
  public function notFound()
  {
    http_response_code(404);

    $title = "404 - Trang không tìm thấy";
    $defaultMessage = "Xin lỗi, trang bạn đang tìm kiếm không tồn tại.";
    $message = session_get_flash('error_message') ?: $defaultMessage;

    $data = [
      'title' => $title,
      'message' => $message,
    ];

    render_view('error/404', $data, 'error');
  }

  public function forbidden()
  {
    http_response_code(403);
    $title = "403 - Truy cập bị từ chối";
    $defaultMessage = "Bạn không có quyền truy cập vào tài nguyên này.";
    $message = session_get_flash('error_message') ?: $defaultMessage;

    $data = [
      'title' => $title,
      'message' => $message,
    ];

    render_view('error/403', $data, 'error');
  }

  public function internalServerError()
  {
    $title = "500 - Lỗi máy chủ nội bộ";
    $defaultMessage = "Đã xảy ra lỗi máy chủ nội bộ. Vui lòng thử lại sau.";
    $message = session_get_flash('error_message') ?: $defaultMessage;

    $data = [
      'title' => $title,
      'message' => $message,
    ];

    render_view('error/500', $data, 'error');
  }
}

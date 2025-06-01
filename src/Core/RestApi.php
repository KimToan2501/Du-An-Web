<?php

namespace App\Core;

class RestApi
{
  static function setHeaders($isUpload = false)
  {
    // Configure headers
    
    if (!$isUpload) {
      header('Content-Type: application/json');
      header('Access-Control-Allow-Origin: *');
      header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
      header('Access-Control-Allow-Headers: Content-Type');
      return;
    }


    // set upload file
    header("Content-Type: multipart/form-data");
    header("Accept: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
  }

  static function getBody()
  {
    $input = json_decode(file_get_contents('php://input'), true);
    return $input;
  }

  static function response($data, $status = 200)
  {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
  }

  static function responseError($message, $status = 400)
  {
    $data = [
      'message' => $message,
      'status' => $status
    ];
    self::response($data, $status);
  }

  static function responseSuccess($metadata, $message = 'Success', $status = 200)
  {
    $data = [
      'message' => $message,
      'status' => $status,
      'metadata' => $metadata
    ];

    self::response($data, $status);
  }
}

<?php

use App\Controllers\Error\ErrorController;
use App\models\Account;

session_start();

// Define the root directory
define('ROOT_DIR', dirname(__DIR__)); // This points to the parent directory of public
define('VIEW_DIR', ROOT_DIR . '/src/Views');

// Configure timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');


// http://localhost:8080/assets/css/admin-common.css



require_once ROOT_DIR . '/vendor/autoload.php';
require_once ROOT_DIR . '/src/utils/lib.php';
$globalConfigs = require_once ROOT_DIR . '/src/utils/configs.php';

// Setup error handling trước khi load các file khác
set_error_handler(function ($severity, $message, $file, $line) {
  error_log("File: " . $file);

  // Log toàn bộ stack trace
  error_log("Message: " . $message);

  // Hoặc log tất cả thông tin
  error_log("line: " . $line);
  // Chuyển PHP errors thành exceptions
  throw new ErrorException($message, 0, $severity, $file, $line);
});

// Setup exception handler
set_exception_handler(function ($exception) {
  error_log("Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());

  // Show 500 error page
  try {
    $errorController = new ErrorController();
    session_set_flash('error_message', 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.');
    $errorController->internalServerError();
  } catch (Exception $e) {
    // Log vị trí xảy ra lỗi (file và dòng)
    error_log("File: " . $e->getFile() . " Line: " . $e->getLine());

    // Log toàn bộ stack trace
    error_log("Stack trace: " . $e->getTraceAsString());

    // Hoặc log tất cả thông tin
    error_log("Full exception: " . $e->__toString());
    // Fallback nếu không thể load error controller
    http_response_code(500);
    echo "<!DOCTYPE html>
      <html>
      <head><title>500 - Server Error</title></head>
      <body style='font-family: Arial; text-align: center; padding: 50px;'>
          <h1>500 - Server Error</h1>
          <p>Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.</p>
          <a href='/' style='color: #3498db;'>Về trang chủ</a>
      </body>
      </html>";
  }
  exit;
});
try {


  $dotenv = Dotenv\Dotenv::createImmutable(ROOT_DIR, '.env.local');
  $dotenv->load();

  define('BASE_URL', $_ENV['BASE_URL'] ?: 'http://localhost:8080');

  // Connect to database
  $servername = $_ENV['DB_HOST'];
  $username = $_ENV['DB_USERNAME'];
  $password = $_ENV['DB_PASSWORD'];
  $dataname = $_ENV['DB_DATABASE'];

  try {
    $PDO = new PDO("mysql:host=$servername;dbname=$dataname", $username, $password);
    // set the PDO error mode to exception
    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
  } catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());

    // Log vị trí xảy ra lỗi (file và dòng)
    error_log("File: " . $e->getFile() . " Line: " . $e->getLine());

    // Log toàn bộ stack trace
    error_log("Stack trace: " . $e->getTraceAsString());

    // Hoặc log tất cả thông tin
    error_log("Full exception: " . $e->__toString());

    // Show 500 error với message cụ thể
    $errorController = new ErrorController();
    session_set_flash('error_message', 'Không thể kết nối cơ sở dữ liệu. Vui lòng thử lại sau.');
    $errorController->internalServerError();
    exit;
  }

  // Check account admin
  Account::findOrCreateAdmin();

  // Create Router instance
  $router = new \Bramus\Router\Router();

  // Define routes
  require_once ROOT_DIR . '/src/routes/web.php';

  // Run it!
  $router->run();
} catch (Exception $e) {
  // Catch any other exceptions
  error_log("Application Exception: " . $e->getMessage());

  // Log vị trí xảy ra lỗi (file và dòng)
  error_log("File: " . $e->getFile() . " Line: " . $e->getLine());

  // Log toàn bộ stack trace
  error_log("Stack trace: " . $e->getTraceAsString());

  // Hoặc log tất cả thông tin
  error_log("Full exception: " . $e->__toString());

  // Show 500 error page
  $errorController = new ErrorController();
  session_set_flash('error_message', 'Đã xảy ra lỗi trong ứng dụng. Vui lòng thử lại sau.');
  $errorController->internalServerError();

  // Final fallback error handling
  http_response_code(500);
  echo "<!DOCTYPE html>
   <html>
   <head>
       <title>500 - Application Error</title>
       <style>
           body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f8f9fa; }
           .error-container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
           .error-code { font-size: 48px; color: #e74c3c; margin-bottom: 20px; }
           .error-message { color: #666; margin-bottom: 30px; }
           .back-btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
       </style>
   </head>
   <body>
       <div class='error-container'>
           <div class='error-code'>500</div>
           <h1>Lỗi Ứng Dụng</h1>
           <p class='error-message'>Đã xảy ra lỗi nghiêm trọng. Vui lòng liên hệ quản trị viên.</p>
           <a href='/' class='back-btn'>Về Trang Chủ</a>
       </div>
   </body>
   </html>";
}

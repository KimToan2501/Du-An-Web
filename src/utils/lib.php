<?php

/**
 * Render a view
 *
 * @param string $view The view name
 * @param array $data The data to pass to the view
 * @param string $layout The layout to use (null for no layout)
 *
 * @return void
 */
function render_view(string $view, array $data = [], $layout = null)
{
  $path = VIEW_DIR . '/' . $view . '.php';

  if (!file_exists($path)) {
    throw new Exception('View not found: ' . $view);
  }

  // Extract variables from $data
  extract($data, EXTR_PREFIX_SAME, '__var_');

  // If no layout is specified, just include the view
  if ($layout === null) {
    require $path;
    return;
  }

  // Check if the layout exists
  $layoutPath = VIEW_DIR . '/layouts/' . $layout . '.php';
  if (!file_exists($layoutPath)) {
    throw new Exception('Layout not found: ' . $layout);
  }

  // Start output buffering to capture the view content
  ob_start();
  require $path;
  $content = ob_get_clean();

  // Make the content available to the layout
  $data['content'] = $content;
  extract($data, EXTR_PREFIX_SAME, '__var_');

  // Include the layout
  require $layoutPath;
}

/**
 * Get the PDO instance
 *
 * @return \PDO
 */
function PDO(): \PDO
{
  global $PDO;
  return $PDO;
}

/**
 * Tạo URL đầy đủ cho tài nguyên
 *
 * @param string $path Đường dẫn tương đối (không bắt đầu bằng /)
 * @return string URL đầy đủ
 */
function base_url(string $path = ''): string
{
  // Lấy URL cơ sở từ biến môi trường hoặc cấu hình
  $baseUrl = defined('BASE_URL') ? BASE_URL : '';

  // Nếu không có BASE_URL được định nghĩa, tự động xác định từ $_SERVER
  if (empty($baseUrl)) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

    // Lấy thư mục gốc của ứng dụng (thường là /public)
    $baseDir = dirname($scriptName);

    // Nếu baseDir là '/', đặt nó thành chuỗi rỗng để tránh URL có '//'
    $baseDir = $baseDir === '/' ? '' : $baseDir;

    $baseUrl = "{$protocol}://{$host}{$baseDir}";
  }

  // Đảm bảo path không bắt đầu bằng '/' để tránh URL có '//'
  $path = ltrim($path, '/');

  // Nếu path không rỗng và baseUrl không kết thúc bằng '/', thêm '/'
  if (!empty($path) && substr($baseUrl, -1) !== '/') {
    return "{$baseUrl}/{$path}";
  }

  return $baseUrl . $path;
}

/**
 * Include a partial view
 *
 * @param string $partial The partial view name
 * @param array $data The data to pass to the partial
 *
 * @return void
 */
function include_partial(string $partial, array $data = [])
{
  $path = VIEW_DIR . '/partials/' . $partial . '.php';

  if (!file_exists($path)) {
    throw new Exception('Partial not found: ' . $partial);
  }

  // Extract variables from $data
  extract($data, EXTR_PREFIX_SAME, '__var_');

  // Include the partial
  require $path;
}

/**
 * Extend a section in a layout
 *
 * @param string $name The section name
 * @param callable|string $callback The callback or content
 *
 * @return void
 */
function section(string $name, $callback = null)
{
  global $sections;

  if (!isset($sections)) {
    $sections = [];
  }

  if ($callback === null) {
    echo $sections[$name] ?? '';
    return;
  }

  if (isset($sections[$name])) {
    echo $sections[$name];
    return;
  }

  ob_start();

  if (is_string($callback)) {
    echo $callback;
  } else {
    call_user_func($callback);
  }

  $sections[$name] = ob_get_clean();
}

/**
 * Start a section
 *
 * @param string $name The section name
 *
 * @return void
 */
function start_section(string $name)
{
  global $currentSection;
  $currentSection = $name;
  ob_start();
}

/**
 * End a section
 *
 * @return void
 */
function end_section()
{
  global $currentSection;

  if (!isset($currentSection)) {
    throw new Exception('No section started');
  }

  $content = ob_get_clean();

  section($currentSection, $content);
  $currentSection = null;
}

/**
 * Kiểm tra và trả về class 'active' nếu URL hiện tại khớp với đường dẫn được chỉ định
 *
 * @param string $path Đường dẫn cần kiểm tra
 * @param string $class Tên class sẽ trả về nếu khớp (mặc định là 'active')
 * @param bool $exact Kiểm tra khớp chính xác hay chỉ cần chứa đường dẫn
 * @return string Class 'active' nếu URL hiện tại khớp với đường dẫn, ngược lại trả về chuỗi rỗng
 */
function active_link(string $path, string $class = 'active', bool $exact = false): string
{
  // Lấy URL hiện tại
  $currentUrl = $_SERVER['REQUEST_URI'];

  // Loại bỏ query string nếu có
  if (($pos = strpos($currentUrl, '?')) !== false) {
    $currentUrl = substr($currentUrl, 0, $pos);
  }

  // Loại bỏ BASE_URL nếu có trong $path
  $baseUrl = defined('BASE_URL') ? BASE_URL : '';
  if (!empty($baseUrl) && strpos($path, $baseUrl) === 0) {
    $path = substr($path, strlen($baseUrl));
  }

  // Đảm bảo cả hai đường dẫn đều bắt đầu bằng '/'
  $currentUrl = '/' . ltrim($currentUrl, '/');
  $path = '/' . ltrim($path, '/');

  // Kiểm tra khớp
  if ($exact) {
    // Khớp chính xác
    return ($currentUrl === $path) ? $class : '';
  } else {
    // Khớp một phần (URL hiện tại bắt đầu bằng $path)
    // Hoặc $path là trang chủ '/' và URL hiện tại cũng là trang chủ
    if ($path === '/' && $currentUrl === '/') {
      return $class;
    }

    // Kiểm tra xem URL hiện tại có bắt đầu bằng $path không (trừ trường hợp $path là '/')
    if ($path !== '/' && (strpos($currentUrl, $path) === 0)) {
      return $class;
    }
  }

  return '';
}

function dd($data)
{
  echo '<pre>';
  var_dump($data);
  echo '</pre>';
  die();
}

function redirect($url)
{
  header('Location: ' . $url);
  exit();
}

function Configs($key = null)
{
  global $globalConfigs;
  return $globalConfigs[$key];
}

function format_price($value)
{
  // Chuyển đổi giá trị sang số nguyên hoặc float
  $value = (float) $value;

  // Định dạng số với dấu phân cách hàng nghìn và thêm đơn vị VNĐ
  return number_format($value, 0, ',', '.') . ' VNĐ';
}

function to_int($value)
{
  return (int) $value;
}

function format_day_vn($dateSelected)
{
  $daysVN = [
    'Sunday' => 'Chủ nhật',
    'Monday' => 'Thứ hai',
    'Tuesday' => 'Thứ ba',
    'Wednesday' => 'Thứ tư',
    'Thursday' => 'Thứ năm',
    'Friday' => 'Thứ sáu',
    'Saturday' => 'Thứ bảy'
  ];

  $timestamp = strtotime($dateSelected);
  return $daysVN[date('l', $timestamp)] . ', ' . date('d/m/Y', $timestamp);
}

function format_date($value, $format = 'd/m/Y')
{
  $date = new DateTime($value);
  return $date->format($format);
}

function show_avatar($avatarPath)
{
  return base_url($avatarPath ? $avatarPath : '/assets/images/avatar-default.png');
}

function show_pet_avatar($avatarPath)
{
  return base_url($avatarPath ? $avatarPath : '/assets/images/default-pet.jpg');
}

function session_get_flash(string $key, $default = null)
{
  $message = $default;

  if (isset($_SESSION[$key])) {
    $message = $_SESSION[$key];
    unset($_SESSION[$key]);
  }

  return $message;
}

function session_set_flash(string $key, $value)
{
  $_SESSION[$key] = $value;
}

function show_404($message = null)
{
  if ($message) {
    session_set_flash('error_message', $message);
  }

  $errorController = new \App\Controllers\Error\ErrorController();
  $errorController->notFound();
  exit;
}

function show_403($message = null)
{
  if ($message) {
    session_set_flash('error_message', $message);
  }

  $errorController = new \App\Controllers\Error\ErrorController();
  $errorController->forbidden();
  exit;
}

function show_500($message = null)
{
  if ($message) {
    session_set_flash('error_message', $message);
  }

  $errorController = new \App\Controllers\Error\ErrorController();
  $errorController->internalServerError();
  exit;
}

function getGenderName($gender)
{
  $genderNames = [
    'male' => 'Đực',
    'female' => 'Cái',
    'unknown' => 'Không xác định',
  ];

  return $genderNames[$gender] ?? 'Khác';
}

// Helper functions - these should be added to your helpers or included globally
function getBookingStatusName($status)
{
  $statuses = [
    'pending' => 'Chờ xác nhận',
    'confirmed' => 'Đã xác nhận',
    'in_progress' => 'Đang thực hiện',
    'completed' => 'Hoàn thành',
    'cancelled' => 'Đã hủy',
    'no_show' => 'Không đến'
  ];
  return $statuses[$status] ?? $status;
}

function getPaymentStatusName($status)
{
  $statuses = [
    'pending' => 'Chờ thanh toán',
    'paid' => 'Đã thanh toán',
    'failed' => 'Thanh toán thất bại'
  ];
  return $statuses[$status] ?? $status;
}

function getPaymentMethodName($method)
{
  $methods = [
    'cash' => 'Tiền mặt',
    'vnpay' => 'VNPay',
    'momo' => 'MoMo',
    'bank_transfer' => 'Chuyển khoản'
  ];
  return $methods[$method] ?? $method;
}

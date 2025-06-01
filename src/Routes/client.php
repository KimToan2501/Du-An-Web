<?php

use App\controllers\client\BlogController;
use App\Controllers\Client\BookingController;
use App\Controllers\Admin\BookingController as BookingAdminController;
use App\Controllers\Api\ProfileApiController;
use App\Controllers\Client\CartController;
use App\Controllers\Client\DiscountController;
use App\Controllers\Client\HomeController;
use App\controllers\client\PetController;
use App\Controllers\Client\ProfileController;
use App\Controllers\Client\ReviewController;
use App\Controllers\Client\ServiceController;
use App\Middlewares\AuthMiddleware;

$router->before('GET|POST', '/user.*', function () {
  AuthMiddleware::requireWebAuth();
});

$router->before('GET|POST', '/booking.*', function () {
  AuthMiddleware::requireWebAuth();
});

// Middleware cho các routes cần auth
$authRequiredRoutes = [
  'GET' => ['/cart/info', '/cart/staff', '/cart/finish', '/cart/finish/success', '/booking/review'],
  'POST' => ['/cart/save-booking-info', '/cart/clear-booking-info', '/cart/save-customer-info', '/cart/save-staff-schedule', '/cart/confirm-booking']
];

foreach ($authRequiredRoutes as $method => $routes) {
  foreach ($routes as $route) {
    $router->before($method, $route, function () {
      AuthMiddleware::requireWebAuth();
    });
  }
}

$router->get('/',  HomeController::class . '@index');
$router->get('/introduce',  HomeController::class . '@introduce');
$router->get('/contact',  HomeController::class . '@contact');

// blog
$router->get('/blog',  BlogController::class . '@index');
$router->get('/blog/search', BlogController::class . '@search');
$router->get('/blog/([a-zA-Z0-9\-_]+)', BlogController::class . '@detail');

$router->get('/service', ServiceController::class . '@index');
$router->get('/service/detail/{id}', ServiceController::class . '@detail');

// cart
$router->get('/cart', CartController::class . '@index');
$router->get('/cart/add/{id}', CartController::class . '@add');
$router->get('/cart/remove/{id}', CartController::class . '@remove');
$router->post('/cart/update/quantity', CartController::class . '@updateQuantity');

$router->get('/cart/info', CartController::class . '@info');
$router->get('/cart/staff', CartController::class . '@staff');
$router->get('/cart/finished', CartController::class . '@finish');
$router->get('/cart/finished/success', CartController::class . '@success');

// Routes mới cho session booking info
$router->post('/cart/save-booking-info', CartController::class . '@saveBookingInfo');
$router->post('/cart/clear-booking-info', CartController::class . '@clearBookingInfo');
$router->post('/cart/save-customer-info', CartController::class . '@saveCustomerInfo');
$router->post('/cart/save-staff-schedule', CartController::class . '@saveStaffSchedule');
$router->post('/cart/confirm-booking', CartController::class . '@confirmBooking');

// discount
$router->post('/discount/check-code', DiscountController::class . '@checkCode');

// Pet Routes
$router->get('/user/pets', PetController::class . '@index');
$router->get('/user/pets/create', PetController::class . '@create');
$router->post('/user/pets', PetController::class . '@store');
$router->get('/user/pets/edit/{id}', PetController::class . '@edit');
$router->get('/user/pets/{id}', PetController::class . '@show');
$router->post('/user/pets/{id}', PetController::class . '@update');
$router->delete('/user/pets/{id}', PetController::class . '@destroy');

// Profile API Routes
$router->post('/api/user/update-profile', ProfileApiController::class . '@updateProfile');
$router->post('/api/user/change-password', ProfileApiController::class . '@changePassword');

// Profile Pages Routes  
$router->get('/user/profile', ProfileController::class . '@index');
$router->get('/user/profile/edit', ProfileController::class . '@edit');

// Booking Routes
$router->get('/user/booking', BookingController::class . '@index');
$router->get('/booking/response', BookingController::class . '@response');
$router->post('/booking/review', ReviewController::class . '@store');

$router->post('/booking/quick-update/(\d+)', BookingAdminController::class . '@quickUpdate');

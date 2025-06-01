<?php

use App\Controllers\Admin\BlogController;
use App\Controllers\Admin\ServiceController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ServiceTypeController;
use App\Controllers\Admin\StaffController;
use App\Controllers\Admin\StaffScheduleController;
use App\Controllers\Admin\CustomerController;
use App\Controllers\Admin\DiscountController;
use App\Controllers\Admin\BookingController;
use App\Middlewares\AuthMiddleware;

$router->before('GET|POST', '/admin.*', function () {
    AuthMiddleware::requireWebAuth();
    AuthMiddleware::unAccessCustomer();
});

$router->mount('/admin', function () use ($router) {

    // Dashboard
    $router->get('/dashboard', DashboardController::class . '@index');
    $router->get('/dashboard/revenue-chart', DashboardController::class . '@getRevenueChartData');
    $router->get('/dashboard/service-chart', DashboardController::class . '@getServiceChartData');

    // Service
    $router->get('/service', ServiceController::class . '@index');
    $router->get('/service/add', ServiceController::class . '@showAdd');
    $router->post('/service/add', ServiceController::class . '@add');
    $router->get('/service/update/(\d+)', ServiceController::class . '@showUpdate');
    $router->post('/service/update/(\d+)', ServiceController::class . '@edit');
    $router->delete('/service/(\d+)', ServiceController::class . '@delete');
    $router->delete('/service/image/{id}', ServiceController::class . '@deleteImage');

    // ServiceType
    $router->get('/service-type', ServiceTypeController::class . '@index');
    $router->get('/service-type/add', ServiceTypeController::class . '@showAdd');
    $router->post('/service-type/add', ServiceTypeController::class . '@add');
    $router->get('/service-type/update/(\d+)', ServiceTypeController::class . '@showEdit');
    $router->post('/service-type/update/(\d+)', ServiceTypeController::class . '@edit');
    $router->delete('/service-type/(\d+)', ServiceTypeController::class . '@delete');

    // Staff
    $router->get('/staff', StaffController::class . '@index');
    $router->get('/staff/add', StaffController::class . '@showAdd');
    $router->post('/staff/add', StaffController::class . '@add');
    $router->get('/staff/update/(\d+)', StaffController::class . '@showUpdate');
    $router->post('/staff/update/(\d+)', StaffController::class . '@edit');
    $router->delete('/staff/(\d+)', StaffController::class . '@delete');

    // Staff Schedule
    $router->get('/staff/schedule/(\d+)', StaffScheduleController::class . '@index');
    $router->get('/staff/add/schedule/(\d+)', StaffScheduleController::class . '@showAdd');
    $router->post('/staff/add/schedule/(\d+)', StaffScheduleController::class . '@add');
    $router->get('/staff/update/schedule/(\d+)/([a-z0-9_-]+)', StaffScheduleController::class . '@showUpdate');
    $router->post('/staff/update/schedule/(\d+)/([a-z0-9_-]+)', StaffScheduleController::class . '@edit');
    $router->delete('/staff/schedule/delete/(\d+)/(\d+)', StaffScheduleController::class . '@delete');

    // Customer
    $router->get('/customer', CustomerController::class . '@index');
    $router->get('/customer/add', CustomerController::class . '@showAdd');
    $router->post('/customer/add', CustomerController::class . '@add');
    $router->get('/customer/update/(\d+)', CustomerController::class . '@showUpdate');
    $router->post('/customer/update/(\d+)', CustomerController::class . '@edit');
    $router->delete('/customer/(\d+)', CustomerController::class . '@delete');

    // Discount
    $router->get('/discount', DiscountController::class . '@index');
    $router->get('/discount/add', DiscountController::class . '@showAdd');
    $router->post('/discount/add', DiscountController::class . '@add');
    $router->get('/discount/update/(\d+)', DiscountController::class . '@showUpdate');
    $router->post('/discount/update/(\d+)', DiscountController::class . '@edit');
    $router->delete('/discount/(\d+)', DiscountController::class . '@delete');

    // Booking
    $router->get('/booking', BookingController::class . '@index');
    $router->get('/booking/add', BookingController::class . '@showAdd');
    $router->post('/booking/add', BookingController::class . '@add');
    $router->get('/booking/(\d+)', BookingController::class . '@show');
    $router->get('/booking/update/(\d+)', BookingController::class . '@showUpdate');
    $router->post('/booking/update/(\d+)', BookingController::class . '@edit');
    $router->delete('/booking/(\d+)', BookingController::class . '@delete');
    $router->post('/booking/quick-update/(\d+)', BookingController::class . '@quickUpdate');

    // Blog
    $router->get('/blog', BlogController::class . '@index');
    $router->get('/blog/add', BlogController::class . '@showAdd');
    $router->post('/blog/add', BlogController::class . '@add');
    $router->get('/blog/update/(\d+)', BlogController::class . '@showUpdate');
    $router->post('/blog/update/(\d+)', BlogController::class . '@edit');
    $router->get('/blog/details/(\d+)', BlogController::class . '@show');
    $router->delete('/blog/(\d+)', BlogController::class . '@delete');

    $router->post('/blog/upload-image', BlogController::class . '@uploadImage');
});

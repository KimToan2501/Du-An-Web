<?php

use App\Controllers\Auth\AuthController;

$router->get('/login', AuthController::class . '@showLogin');
$router->post('/login', AuthController::class . '@login');

$router->get('/register', AuthController::class . '@showRegister');
$router->post('/register', AuthController::class . '@register');

$router->get('/register/otp', AuthController::class . '@showRegisterOtp');
$router->post('/register/otp/verify', AuthController::class . '@verifyOtp');
$router->post('/register/otp/resend', AuthController::class . '@resendOtp');

$router->get('/forgot-password', AuthController::class. '@showForgotPassword');
$router->post('/forgot-password', AuthController::class. '@forgotPassword');

$router->get('/reset-password', AuthController::class. '@showResetPassword');
$router->post('/reset-password', AuthController::class. '@resetPassword');

$router->post('/api/logout', AuthController::class . '@logout');
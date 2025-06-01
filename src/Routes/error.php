
<?php

// Tạo instance của ErrorController

use App\Controllers\Error\ErrorController;

$errorController = new ErrorController();

// Setup 404 handler
$router->set404(function () use ($errorController) {
    $errorController->notFound();
});

// Setup các route error khác (tùy chọn)
$router->get('/403', function () use ($errorController) {
    $errorController->forbidden();
});

$router->get('/500', function () use ($errorController) {
    $errorController->internalServerError();
});

<?php

use App\Http\Controllers\InfoController;
use App\Http\Controllers\MainController;

$router->exact('/start', MainController::class, 'start');
$router->exact('/info', InfoController::class, 'get');
$router->after('/info', InfoController::class, 'send');

<?php
require_once __DIR__.'/../vendor/autoload.php';

use App\Core\Router;

// Инициализация приложения
$router = new Router();
$router->dispatch();
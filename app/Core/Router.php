<?php

namespace App\Core;

use App\Controllers\ErrorController;

class Router
{
    private $routes = [];

    public function __construct()
    {
        $this->initializeRoutes();
    }

    private function initializeRoutes()
    {
        // Явно прописываем главный маршрут
        $this->addRoute('GET', '/', 'CryptocurrencyController@index');
    }

    public function addRoute(string $method, string $pattern, string $handler)
    {
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // Получаем только путь без параметров
        $uri = trim($uri, '/');

        // Для главной страницы
        if ($uri === '' && $method === 'GET') {
            $uri = '/';
        }

        foreach ($this->routes[$method] ?? [] as $route) {
            $pattern = $this->buildPattern($route['pattern']);

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                return $this->callHandler($route['handler'], $matches);
            }
        }

        $this->handleNotFound();
    }


    private function buildPattern(string $pattern): string
    {
        return '@^' . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($pattern)) . '$@D';
    }

    private function callHandler(string $handler, array $params)
    {
        [$controllerName, $methodName] = explode('@', $handler);
        $controllerClass = 'App\\Controllers\\' . $controllerName;

        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();

            if (method_exists($controller, $methodName)) {
                return call_user_func_array([$controller, $methodName], $params);
            }
        }

        $this->handleNotFound();
    }

    private function handleNotFound()
    {
        header("HTTP/1.0 404 Not Found");
        try {
            $controller = new ErrorController();
            $controller->notFound();
        } catch (\Exception $e) {
            // Фолбэк, если даже страница ошибки не работает
            die('404 Page Not Found');
        }
    }
}

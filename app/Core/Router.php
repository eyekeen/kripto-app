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
        $this->addRoute('GET', '/', 'CryptocurrencyController@index');
        $this->addRoute('GET', '/cryptocurrencies', 'CryptocurrencyController@index');
        $this->addRoute('GET', '/cryptocurrencies/([a-zA-Z0-9]+)', 'CryptocurrencyController@show');
        $this->addRoute('GET', '/api/cryptocurrencies', 'CryptocurrencyController@apiIndex');
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
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = trim($uri, '/');
        
        foreach ($this->routes[$method] ?? [] as $route) {
            $pattern = '@^' . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($route['pattern'])) . '$@D';
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                
                $handlerParts = explode('@', $route['handler']);
                $controllerName = 'App\\Controllers\\' . $handlerParts[0];
                $methodName = $handlerParts[1];
                
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    
                    if (method_exists($controller, $methodName)) {
                        call_user_func_array([$controller, $methodName], $matches);
                        return;
                    }
                }
            }
        }
        
        // Если маршрут не найден
        $this->handleNotFound();
    }
    
    private function handleNotFound()
    {
        header("HTTP/1.0 404 Not Found");
        $controller = new ErrorController();
        $controller->notFound();
    }
}
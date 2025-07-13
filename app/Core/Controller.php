<?php
namespace App\Core;

class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        $view = new View($view, $data, $layout);
        $view->render();
    }
}
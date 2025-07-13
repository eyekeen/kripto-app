<?php

namespace App\Core;

class View
{
    private string $viewPath;
    private array $data;
    private string $layout;

    public function __construct(string $view, array $data = [], string $layout = 'main')
    {
        $this->viewPath = $this->resolveViewPath($view);
        $this->data = $data;
        $this->layout = $this->resolveLayoutPath($layout);
    }

    private function resolveViewPath(string $view): string
    {
        $path = __DIR__ . '/../Views/' . $view . '.php';
        if (!file_exists($path)) {
            throw new \RuntimeException("View file {$path} not found");
        }
        return $path;
    }

    private function resolveLayoutPath(string $layout): string
    {
        $path = __DIR__ . '/../Views/layouts/' . $layout . '.php';

        // Если запрошенный лейаут не существует, используем error layout
        if (!file_exists($path)) {
            $fallbackPath = __DIR__ . '/../Views/layouts/error.php';
            if (!file_exists($fallbackPath)) {
                throw new \RuntimeException("Neither requested layout {$path} nor fallback error layout found");
            }
            return $fallbackPath;
        }

        return $path;
    }


    public function render(): void
    {
        extract($this->data);

        // Устанавливаем title по умолчанию
        $title = $title ?? 'Crypto Market Cap';

        // Захватываем содержимое view
        ob_start();
        require $this->viewPath;
        $content = ob_get_clean();

        // Включаем layout
        require $this->layout;
    }

    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

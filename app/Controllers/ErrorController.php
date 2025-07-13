<?php

namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller
{
    public function notFound()
    {
        // error_log('Server Error: ' . $exception->getMessage());
        $this->render('errors/404', [
            'title' => 'Page Not Found'
        ], 'error'); // Используем error layout
    }

    public function serverError()
    {
        // error_log('Server Error: ' . $exception->getMessage());
        $this->render('errors/500', [
            'title' => 'Server Error'
        ], 'error'); // Используем error layout
    }
}

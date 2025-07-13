<?php
namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller
{
    public function notFound()
    {
        $this->render('errors/404', [], 404);
    }
    
    public function serverError()
    {
        $this->render('errors/500', [], 500);
    }
}
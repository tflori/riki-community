<?php

namespace App\Http\Controller;

use Psr\Http\Message\ServerRequestInterface;

class HomeController extends AbstractController
{
    public function getHome(ServerRequestInterface $request)
    {
        return $this->view('pages/home');
    }
}

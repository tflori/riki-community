<?php

namespace App\Http\Controller;

class HomeController extends AbstractController
{
    public function getHome()
    {
        return $this->view('pages/home');
    }
}

<?php

namespace App\Http\Controller;

use App\Model\Request;
use Community\Model\User;

class AuthController extends AbstractController
{
    public function getUser()
    {
        $session = $this->app->session;
        return $this->json($session->get('user'));
    }
}

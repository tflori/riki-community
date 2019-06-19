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

    public function authenticate(Request $request)
    {
        $authData = $request->getJson();

        // @todo throttle login attempts for ip
        $em = $this->app->entityManager;
        /** @var User $user */
        $user = $em->fetch(User::class)
            ->where('email', $authData['email'] ?? '')
            ->one();
        if (!$user) {
            return $this->error(400, 'Bad Request', 'Authentication failed');
        }

        // @todo throttle login attempts for user
        if (!password_verify($authData['password'] ?? '', $user->password)) {
            return $this->error(400, 'Bad Request', 'Authentication failed');
        }

        $this->app->session->set('user', $user);
        return $this->json($user);
    }
}

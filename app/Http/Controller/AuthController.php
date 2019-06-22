<?php

namespace App\Http\Controller;

use Community\Model\User;
use Tal\ServerResponse;

class AuthController extends AbstractController
{
    public function getUser(): ServerResponse
    {
        $session = $this->app->session;
        return $this->json($session->get('user'));
    }

    public function authenticate(): ServerResponse
    {
        $authData = $this->request->getJson();

        // @todo throttle login attempts for ip
        /** @var User $user */
        $user = $this->app->entityManager->fetch(User::class)
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

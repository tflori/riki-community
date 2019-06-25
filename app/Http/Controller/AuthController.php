<?php

namespace App\Http\Controller;

use Community\Model\User;
use Tal\ServerResponse;

class AuthController extends AbstractController
{
    const LOGIN_ATTEMPTS_KEY = 'LoginAttempts_%s_%s';
    const LOGIN_ATTEMPTS_LIMITS = [
        'ip'   => [10 => 3, 60 => 10],
        'user' => [10 => 3, 300 => 10],
    ];

    public function getUser(): ServerResponse
    {
        $session = $this->app->session;
        return $this->json($session->get('user'));
    }

    public function authenticate(): ServerResponse
    {
        $authData = $this->request->getJson();

        $key = sprintf(self::LOGIN_ATTEMPTS_KEY, 'ip', $this->request->getIp());
        $ipAuthAttempts = $this->app->cache->get($key, []);
        foreach (self::LOGIN_ATTEMPTS_LIMITS['ip'] as $seconds => $limit) {
            if ($this->countAttemptsWithinSeconds($ipAuthAttempts, $seconds) >= $limit) {
                return $this->error(423, 'Locked', 'Too many authentication attempts');
            }
        }

        /** @var User $user */
        $user = $this->app->entityManager->fetch(User::class)
            ->where('email', $authData['email'] ?? '')
            ->one();
        if (!$user) {
            $ipAuthAttempts[] = time();
            $this->app->cache->set($key, $ipAuthAttempts, max(array_keys(self::LOGIN_ATTEMPTS_LIMITS['ip'])));
            return $this->error(400, 'Bad Request', 'Authentication failed');
        }

        // @todo throttle login attempts for user
        if (!password_verify($authData['password'] ?? '', $user->password)) {
            return $this->error(400, 'Bad Request', 'Authentication failed');
        }

        $this->app->session->set('user', $user);
        return $this->json($user);
    }

    protected function countAttemptsWithinSeconds(array $attempts, $seconds)
    {
        $earliest = time() - $seconds;
        return count(array_filter($attempts, function ($time) use ($earliest) {
            return $time >= $earliest;
        }));
    }
}

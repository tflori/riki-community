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
        $authAttempts = $this->app->cache->get($key, []);
        if ($this->attemptLimitReached(self::LOGIN_ATTEMPTS_LIMITS['ip'], $authAttempts)) {
            return $this->error(423, 'Locked', 'Too many authentication attempts');
        }

        /** @var User $user */
        $user = $this->app->entityManager->fetch(User::class)
            ->where('email', $authData['email'] ?? '')
            ->one();
        if (!$user) {
            return $this->increaseAttempts($key, $authAttempts);
        }

        $key = sprintf(self::LOGIN_ATTEMPTS_KEY, 'user', $user->id);
        $authAttempts = $this->app->cache->get($key, []);
        if ($this->attemptLimitReached(self::LOGIN_ATTEMPTS_LIMITS['user'], $authAttempts)) {
            return $this->error(423, 'Locked', 'Too many authentication attempts');
        }

        if (!password_verify($authData['password'] ?? '', $user->password)) {
            return $this->increaseAttempts($key, $authAttempts);
        }

        $this->app->session->set('user', $user);
        return $this->json($user);
    }

    public function logout()
    {
        if (!$this->request->getAttribute('csrfTokenVerified', false)) {
            return $this->error(400, 'Bad Request', 'Invalid Request Token');
        }

        $this->app->session->destroy();

        return $this->json(['message' => 'Successfully logged out!']);
    }

    protected function attemptLimitReached(array $limits, array $attempts): bool
    {
        foreach ($limits as $seconds => $limit) {
            if ($this->countAttemptsWithinSeconds($attempts, $seconds) >= $limit) {
                return true;
            }
        }

        return false;
    }

    protected function countAttemptsWithinSeconds(array $attempts, $seconds): int
    {
        $earliest = time() - $seconds;
        return count(array_filter($attempts, function ($time) use ($earliest) {
            return $time >= $earliest;
        }));
    }

    protected function increaseAttempts(string $key, array $attempts)
    {
        $attempts[] = time();
        $this->app->cache->set($key, $attempts, max(
            max(array_keys(self::LOGIN_ATTEMPTS_LIMITS['ip'])),
            max(array_keys(self::LOGIN_ATTEMPTS_LIMITS['user']))
        ));
        return $this->error(400, 'Bad Request', 'Authentication failed');
    }
}

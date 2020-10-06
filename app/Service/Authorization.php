<?php

namespace App\Service;

use App\Application;
use Community\Model\Token\AbstractToken;
use Community\Model\User;

/**
 * @property-read User $user
 * @property-read string $csrfToken
 */
class Authorization
{
    /** @var User */
    protected $user;

    /** @var Application */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /** @codeCoverageIgnore trivial code */
    public function __get(string $name)
    {
        $getter = [$this, 'get' . ucfirst($name)];
        if (is_callable($getter)) {
            return call_user_func($getter);
        }
        return null;
    }

    /** @codeCoverageIgnore trivial code */
    public function isAuthenticated()
    {
        return $this->getUser() !== null;
    }

    public function getUser(): ?User
    {
        $userId = $this->app->session->get('userId');
        if (!$userId) {
            return null;
        }

        if (!$this->user) {
            $this->user = $this->app->cache->remember('user-' . $userId, function () use ($userId) {
                return $this->app->entityManager->fetch(User::class, $userId);
            }, 3600);
        }

        return $this->user;
    }

    public function setUser(User $user): Authorization
    {
        $this->user = $user;
        $this->app->session->set('userId', $user->id);
        return $this;
    }

    public function getCsrfToken(): string
    {
        $token = AbstractToken::generateToken(10);
        // to create a session we store the last created token
        $this->app->session->set('csrfToken', $token);
        $this->app->cache->set('csrfToken-' . $token, session_id());
        return $token;
    }

    public function isCsrfTokenValid(string $token): bool
    {
        if (!$token || !$this->app->session->get('csrfToken') ||
            $this->app->cache->get('csrfToken-' . $token) !== session_id()
        ) {
            return false;
        }

        $this->app->cache->delete('csrfToken-' . $token);
        return true;
    }

    public function reset(): Authorization
    {
        $this->user = false;
        $this->permissions = null;
        return $this;
    }
}

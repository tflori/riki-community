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
    /** @var bool|User */
    protected $user = false;

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

    public function getUser(): ?User
    {
        if ($this->user === false) {
            $userId = $this->app->session->get('userId');

            if (!$userId) {
                return $this->user = null;
            }

//            $cache = $this->app->cache;
//            if (!$cache->has('user_' . $userId)) {
            $this->user = $this->app->entityManager->fetch(User::class, $userId);
//                $cache->set('user_' . $userId, $this->user, 3600);
//            } else {
//                $user = $cache->get('user_' . $userId);
//            }
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

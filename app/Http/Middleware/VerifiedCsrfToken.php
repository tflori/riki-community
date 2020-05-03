<?php

namespace App\Http\Middleware;

use App\Application;
use App\Http\Concerns\GeneratesResponses;
use App\Model\Request;
use Community\Model\Token\AbstractToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class VerifiedCsrfToken implements MiddlewareInterface
{
    use GeneratesResponses;

    /** @var Application */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param Request|ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->get('csrf_token');
        $key   = $this->getKey($token ?? '');
        if (!$token || !$this->app->session->get('csrfToken') ||
            $this->app->cache->get($key) !== session_id()
        ) {
            return $this->error(400, 'Bad Request', 'Invalid CSRF Token');
        }

        $this->app->cache->delete($key);
        return $handler->handle($request);
    }

    public function createToken(): string
    {
        $token = AbstractToken::generateToken(10);
        // to create a session we store the last created token
        $this->app->session->set('csrfToken', $token);
        $this->app->cache->set($this->getKey($token), session_id());
        return $token;
    }

    protected function getKey(string $token)
    {
        return 'csrfToken-' . $token;
    }
}

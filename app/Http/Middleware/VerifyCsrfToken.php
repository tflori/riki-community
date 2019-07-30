<?php

namespace App\Http\Middleware;

use App\Application;
use App\Model\Request;
use Community\Model\Token\AbstractToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class VerifyCsrfToken implements MiddlewareInterface
{
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
        if ($token && $this->app->session->get('csrfToken') &&
            $this->app->cache->get($key) === session_id()
        ) {
            $this->app->cache->delete($key);
            $request = $request->withAttribute('csrfTokenVerified', true);
        }

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

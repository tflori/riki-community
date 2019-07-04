<?php

namespace App\Http\Middleware;

use App\Application;
use App\Model\Request;
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
        if ($token && $this->app->session->get('csrfToken') === $token) {
            $request = $request->withAttribute('csrfTokenVerified', true);
        }

        return $handler->handle($request);
    }
}

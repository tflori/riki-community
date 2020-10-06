<?php

namespace App\Http\Middleware;

use App\Application;
use App\Http\Concerns\GeneratesResponses;
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

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->get('csrf_token');
        if (!$token || !$this->app->auth->isCsrfTokenValid($token)) {
            return $this->error(400, 'Bad Request', 'Invalid CSRF Token');
        }

        return $handler->handle($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Application;
use App\Http\Concerns\GeneratesResponses;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authenticated implements MiddlewareInterface
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
        $user = $this->app->auth->user;
        if (!$user || $user->accountStatus !== $user::ACTIVATED) {
            return $this->error(401, 'Unauthorized', 'This service requires authentication');
        }

        return $handler->handle($request);
    }
}

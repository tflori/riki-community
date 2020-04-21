<?php

namespace App\Http;

use App\Application;
use App\Http\Controller\AbstractController;
use App\Model\Request;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass;
use ReflectionMethod;
use Tal\Psr7Extended\ServerRequestInterface as TalServerRequestInterface;
use Tal\ServerRequest;

class RequestHandler implements RequestHandlerInterface
{
    const REQUEST_TYPES = [
        Request::class,
        ServerRequest::class,
        PsrServerRequestInterface::class,
        TalServerRequestInterface::class
    ];

    /** @var Application */
    protected $app;

    /** @var string */
    protected $class;

    /** @var string */
    protected $method;

    /**
     * RequestHandler constructor.
     *
     * @param Application $app
     * @param string      $class
     * @param string      $method
     */
    public function __construct(Application $app, string $class, string $method)
    {
        $this->app    = $app;
        $this->class  = $class;
        $this->method = $method;
    }


    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(PsrServerRequestInterface $request): ResponseInterface
    {
        $reflection = new ReflectionClass($this->class);
        if (!$reflection->hasMethod($this->method) && !$reflection->hasMethod('__call')) {
            throw new Exception(sprintf('Action %s is unknown in %s', $this->method, $this->class));
        }

        if ($reflection->isSubclassOf(AbstractController::class)) {
            $instance = $this->app->make($this->class, $this->app, $request);
        } else {
            $instance = $this->app->make($this->class);
        }

        $arguments = array_values($request->getAttribute('arguments') ?? []);
        if ($reflection->hasMethod($this->method) && $this->requiresRequest($reflection->getMethod($this->method))) {
            array_unshift($arguments, $request);
        }
        return call_user_func([$instance, $this->method], ...$arguments);
    }

    protected function requiresRequest(ReflectionMethod $method)
    {
        $parameters = $method->getParameters();

        return count($parameters) > 0 &&
            $parameters[0]->hasType() &&
            in_array($parameters[0]->getType()->getName(), self::REQUEST_TYPES);
    }
}

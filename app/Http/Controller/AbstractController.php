<?php

namespace App\Http\Controller;

use App\Application;
use function GuzzleHttp\Psr7\stream_for;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tal\ServerResponse;

abstract class AbstractController implements RequestHandlerInterface
{
    /** @var ServerRequestInterface */
    protected $request;

    /** @var string */
    protected $action;

    public function __construct($action = 'getIndex')
    {
        $this->action = $action;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        $action = $this->action;

        if (!method_exists($this, $action)) {
            throw new \Exception(sprintf('Action %s is unknown in %s', $action, static::class));
        }

        $arguments = $request->getAttribute('arguments') ?? [];
        $response = call_user_func([$this, $action], ...array_values($arguments));

        return $response;
    }

    /**
     * Returns a error response
     *
     * @param int $status
     * @param string $reason
     * @param string $message
     * @param \Throwable $exception
     * @return ServerResponse
     */
    protected function error(int $status, string $reason, string $message, \Throwable $exception = null): ServerResponse
    {
        // @todo check the accept header and format a proper error (html/json,xml)
        return $this->view('error', compact('status', 'reason', 'message', 'exception'), null)
            ->withStatus($status);
    }

    /**
     * Create a response for $view using $data
     *
     * @param string $view
     * @param array $data
     * @param null|string $layout
     * @return ServerResponse
     */
    protected function view(string $view, array $data = [], ?string $layout = 'fullPage'): ServerResponse
    {
        $body = Application::views()->render($view, $data, $layout);
        return new ServerResponse(200, [], $body);
    }
}

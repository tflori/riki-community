<?php

namespace App\Http\Concerns;

use App\Application;
use App\Http\HttpKernel;
use App\Model\ErrorResponse;
use App\Model\Request;
use Exception;
use Tal\ServerResponse;
use Throwable;

trait GeneratesResponses
{
    /**
     * Returns a error response
     *
     * @param int    $status
     * @param string $reason
     * @param string $message
     * @return ErrorResponse
     */
    protected function error(
        int $status,
        string $reason,
        string $message
    ): ErrorResponse {
        return (new ErrorResponse($status, $reason, $message))->setRequest($this->request ?? null);
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

    /**
     * Create a json response for $data
     *
     * $data will directly be passed to json_encode. Make sure that $data can be json_encoded
     *
     * @param mixed $data
     * @param int   $options
     * @param int   $depth
     * @return ServerResponse
     * @see json_encode()
     */
    protected function json($data, int $options = 0, int $depth = 512): ServerResponse
    {
        return new ServerResponse(200, ['Content-Type' => 'application/json'], json_encode($data, $options, $depth));
    }

    /**
     * Create a redirect response to $location with $status
     *
     * @param string $location
     * @param int $status
     * @return ServerResponse
     */
    protected function redirect(string $location, int $status = 302): ServerResponse
    {
        return new ServerResponse($status, [
            'Location' => $location,
        ]);
    }
}

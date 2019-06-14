<?php

namespace App\Http\Controller;

use App\Application;
use App\Model\Request;
use Exception;
use Tal\ServerResponse;
use Throwable;

abstract class AbstractController
{
    /** @var Application */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Returns a error response
     *
     * @param Request   $request
     * @param int       $status
     * @param string    $reason
     * @param string    $message
     * @param array     $errors
     * @param Throwable $exception
     *
     * @return ServerResponse
     */
    protected function error(
        Request $request,
        int $status,
        string $reason,
        string $message,
        array $errors = [],
        Throwable $exception = null
    ): ServerResponse {
        switch ($request->getPreferredContentType(['text/html', 'application/json'])) {
            case 'application/json':
                $data = compact('reason', 'message');
                if (!empty($errors)) {
                    $data['errors'] = $errors;
                }
                if ($exception instanceof Exception) {
                    $data['exception'] = [
                        'type' => get_class($exception),
                        'message' => $exception->getMessage(),
                        'line' => $exception->getFile() . ':' . $exception->getLine(),
                        'trace' => base64_encode($exception->__toString()),
                    ];
                }
                return $this->json($data)
                    ->withStatus($status);
            case 'text/html':
            default:
                return $this->view('error', compact('status', 'reason', 'message', 'exception'), null)
                    ->withStatus($status);
        }
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
}

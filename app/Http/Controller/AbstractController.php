<?php

namespace App\Http\Controller;

use App\Application;
use Exception;
use function GuzzleHttp\Psr7\stream_for;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tal\ServerResponse;
use Throwable;

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
            throw new Exception(sprintf('Action %s is unknown in %s', $action, static::class));
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
     * @param Throwable $exception
     * @return ServerResponse
     */
    protected function error(int $status, string $reason, string $message, Throwable $exception = null): ServerResponse
    {
        switch ($this->getPreferredContentType(['text/html', 'application/json'])) {
            case 'application/json':
                $data = compact('reason', 'message');
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

    /**
     * Get the preferred value from accept header
     *
     * Limit to $possible if necessary. Return $default if nothing possible accepted or header omitted.
     *
     * @param array  $possible
     * @return string|null
     */
    protected function getPreferredContentType(array $possible): ?string
    {
        if (!$this->request->hasHeader('Accept')) {
            return $possible[0];
        }

        $accepted = [];
        foreach (explode(',', $this->request->getHeader('Accept')[0]) as $item) {
            $parameters = explode(';', trim($item));
            $value = trim(array_shift($parameters));

            $q = 1;
            foreach ($parameters as $parameter) {
                $parameter = trim($parameter);
                if (substr($parameter, 0, 2) !== 'q=' || !is_numeric(substr($parameter, 2))) {
                    continue;
                }
                $q = (double)substr($parameter, 2);
            }

            // hack for stable sorting by quality: repeated quality has 0.0001 less quality
            while (isset($accepted[(string)$q])) {
                $q -= 0.0001;
            }
            $accepted[(string)$q] = $value;
        }

        krsort($accepted); // sort by quality
        $accepted = array_intersect($accepted, $possible);
        return array_shift($accepted) ?? $possible[0];
    }

    /**
     * Check parameters with validation
     *
     * Returns true when valid and false when not. $data will be filled with validated data (when valid) and $errors
     * will be filled with Verja\Error (when invalid).
     *
     * $source has to be an array or an existing getter (query, post, json).
     *
     * Usage:
     * ```php
     *   if (!$this->validate(['name' => ['required']], 'post', $data, $errors)) {
     *     // fail and show $errors
     *   }
     *   // use $data
     * ```
     *
     * @param array        $fields
     * @param string|array $source
     * @param array        $data
     * @param array        $errors
     * @return bool
     * @throws InvalidArgumentException
     */
    protected function validate(array $fields, $source = 'query', &$data = [], &$errors = [])
    {
        if (!is_array($source)) {
            if (!method_exists($this, 'get' . ucfirst($source))) {
                throw new InvalidArgumentException(
                    '$source has to be an array or an existing getter (query, post, json)'
                );
            }
            $source = call_user_func([$this, 'get' . ucfirst($source)]);
        }
        $gate = Application::verja();
        $gate->accepts($fields);

        if ($gate->validate($source)) {
            $data = $gate->getData();
            return true;
        }

        $errors = $gate->getErrors();
        return false;
    }

    /**
     * Get all params or the parameter $key from query
     *
     * @param string $key
     * @param mixed $default
     * @return array|mixed
     */
    protected function getQuery(string $key = null, $default = null)
    {
        if (!$key) {
            return $this->request->getQueryParams();
        }

        return $this->request->getQueryParams()[$key] ?? $default;
    }

    /**
     * Get all params or the parameter $key from post body
     *
     * @param string $key
     * @param mixed $default
     * @return array|mixed
     */
    protected function getPost(string $key = null, $default = null)
    {
        if (!$key) {
            return $this->request->getParsedBody();
        }

        return $this->request->getParsedBody()[$key] ?? $default;
    }

    /**
     * Get the parsed json from request body
     *
     * Warning: $assoc defaults to true in this method.
     *
     * @param bool $assoc
     * @param int  $depth
     * @param int  $options
     * @see json_decode()
     * @return mixed
     */
    protected function getJson(bool $assoc = true, int $depth = 512, int $options = 0)
    {
        $data = json_decode((string)$this->request->getBody(), $assoc, $depth, $options);

        if ($data === null && (string)$this->request->getBody() !== 'null') {
            throw new InvalidArgumentException('Invalid json provided in body');
        }

        return $data;
    }
}

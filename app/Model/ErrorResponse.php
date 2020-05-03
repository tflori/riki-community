<?php

namespace App\Model;

use App\Application;
use App\Http\HttpKernel;
use Tal\Server;
use Tal\ServerResponse;
use Throwable;
use function GuzzleHttp\Psr7\stream_for;

class ErrorResponse extends ServerResponse
{
    /** @var Request */
    protected $request;

    /** @var string */
    protected $reason;

    /** @var string */
    protected $message;

    /** @var array */
    protected $errors = [];

    /** @var Throwable */
    protected $exception;

    public function __construct(int $status, string $reason, string $message)
    {
        $this->reason = $reason;
        $this->message = $message;
        parent::__construct($status);
    }

    /** @codeCoverageIgnore trivial */
    public function send(int $bufferSize = 8192, Server $server = null)
    {
        $this->prepareResponse();
        return parent::send($bufferSize, $server);
    }

    public function getBody()
    {
        $this->prepareResponse();
        return $this->stream;
    }


    public function addErrors(array $errors): self
    {
        $this->errors = array_merge($this->errors, $errors);
        return $this;
    }

    public function setException(?Throwable $exception): self
    {
        $this->exception = $exception;
        return $this;
    }

    public function setRequest(?Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function toArray()
    {
        $data = [
            'reason' => $this->reason,
            'message' => $this->message,
        ];
        if (!empty($this->errors)) {
            $data['errors'] = $this->errors;
        }
        if ($this->exception instanceof Throwable) {
            $data['exception'] = [
                'type' => get_class($this->exception),
                'message' => $this->exception->getMessage(),
                'line' => $this->exception->getFile() . ':' . $this->exception->getLine(),
                'trace' => base64_encode($this->exception->__toString()),
            ];
        }

        return $data;
    }

    protected function prepareResponse()
    {
        $this->request = $this->request ?? HttpKernel::currentRequest() ?? Request::fromGlobals();

        switch ($this->request->getPreferredContentType(['text/html', 'application/json'])) {
            case 'application/json':
                // set content type to json
                $this->addHeader('Content-Type', 'application/json');
                // set content
                $this->setBody(stream_for(json_encode($this->toArray())));
                break;

            case 'text/html':
            default:
                $body = Application::views()->render('error', [
                    'status' => $this->statusCode,
                    'reason' => $this->reason,
                    'message' => $this->message,
                    'exception' => $this->exception,
                ]);
                // set content
                $this->setBody(stream_for($body));
                break;
        }
    }
}

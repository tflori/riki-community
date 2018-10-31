<?php

namespace App\Http\Controller;

use App\Application;
use function GuzzleHttp\Psr7\stream_for;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tal\ServerResponse;

/**
 * Example controller not extending abstract controller
 *
 * Simple PSR RequestHandler implementation
 *
 * @package App\Http\Controller
 * @codeCoverageIgnore Just an example
 */
class IndexController implements RequestHandlerInterface
{
    /**
     * Handle the request and return a response.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
//        throw new \Exception('This should happen - it was expected. Sorry!');
        // you may want to create a factory to create responses
        $response = new ServerResponse(200);
        $response->setBody(stream_for($this->loadIndexTemplate()));
        return $response;
    }

    protected function loadIndexTemplate()
    {
        ob_start();
        include Application::environment()->viewPath('index.php');
        return ob_get_clean();
    }
}

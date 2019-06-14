<?php

namespace Test\Unit\Http\Controller;

use App\Http\Controller\ErrorController;
use App\Model\Request;
use InvalidArgumentException;
use Test\TestCase;
use Mockery as m;

class ErrorControllerTest extends TestCase
{
    /** @test */
    public function returns500Response()
    {
        $request = new Request('POST', '/any/path');
        $errorController = new ErrorController($this->app);

        self::assertSame(500, $errorController->unexpectedError($request)->getStatusCode());
    }

    /** @test */
    public function rendersUnexpectedError()
    {
        $request = new Request('POST', '/any/path');
        $errorController = new ErrorController($this->app);

        $body = $errorController->unexpectedError($request)->getBody()->getContents();

        self::assertContains('Unexpected Error', $body);
    }

    /** @test */
    public function returnsJsonWhenPreferred()
    {
        $errorController = new ErrorController($this->app);
        $request = new Request('GET', '/any/path', [
            'accept' => 'application/json',
        ]);

        $body = $errorController->unexpectedError($request)->getBody()->getContents();

        self::assertNotSame('null', $body);
        self::assertNotNull(json_decode($body));
    }

    /** @test */
    public function jsonContainsTheExceptionWhenAvailable()
    {
        $errorController = new ErrorController($this->app);
        $request = (new Request('GET', '/any/path', [
            'accept' => 'application/json',
        ]));

        $body = $errorController->unexpectedError($request, new InvalidArgumentException('This was expected'))
            ->getBody()->getContents();
        $json = json_decode($body, true);

        self::assertArraySubset([
            'exception' => [
                'type' => InvalidArgumentException::class,
                'message' => 'This was expected',
            ]
        ], $json);
    }

    /** @test */
    public function returns404Response()
    {
        $errorController = new ErrorController($this->app);
        $request = new Request('GET', '/any/path');

        self::assertSame(404, $errorController->notFound($request)->getStatusCode());
    }

    /** @test */
    public function rendersNotFoundError()
    {
        $errorController = new ErrorController($this->app);
        $request = new Request('GET', '/any/path');

        $body = $errorController->notFound($request)->getBody()->getContents();

        self::assertContains('File Not Found', $body);
        self::assertContains('/any/path', $body);
    }

    /** @test */
    public function returns405Response()
    {
        $errorController = new ErrorController($this->app);
        $request = (new Request('POST', '/any/path'));

        self::assertSame(405, $errorController->methodNotAllowed($request, ['GET'])->getStatusCode());
    }

    /** @test */
    public function rendersMethodNotAllowed()
    {
        $errorController = new ErrorController($this->app);
        $request = (new Request('POST', '/any/path'));

        $body = $errorController->methodNotAllowed($request, ['GET'])->getBody()->getContents();

        self::assertContains('Method Not Allowed', $body);
        self::assertContains('/any/path', $body);
        self::assertContains('GET', $body);
    }
}

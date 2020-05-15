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
        $request = new Request('GET', '/any/path');
        $errorController = new ErrorController($this->app, $request);

        self::assertSame(500, $errorController->unexpectedError()->getStatusCode());
    }

    /** @test */
    public function rendersUnexpectedError()
    {
        $request = new Request('GET', '/any/path');
        $errorController = new ErrorController($this->app, $request);

        $body = $errorController->unexpectedError()->getBody()->getContents();

        self::assertStringContainsString('Unexpected Error', $body);
    }

    /** @test */
    public function returnsJsonWhenPreferred()
    {
        $request = new Request('GET', '/any/path', [
            'accept' => 'application/json',
        ]);
        $errorController = new ErrorController($this->app, $request);

        $body = $errorController->unexpectedError()->getBody()->getContents();

        self::assertNotSame('null', $body);
        self::assertNotNull(json_decode($body));
    }

    /** @test */
    public function jsonContainsTheExceptionWhenAvailable()
    {
        $request = (new Request('GET', '/any/path', [
            'accept' => 'application/json',
        ]))->withAttribute('arguments', []);
        $errorController = new ErrorController($this->app, $request);

        $body = $errorController->unexpectedError(new InvalidArgumentException('This was expected'))
            ->getBody()->getContents();

        self::assertJson($body);
        self::assertArraySubset([
            'exception' => [
                'type' => InvalidArgumentException::class,
                'message' => 'This was expected',
            ]
        ], json_decode($body, true));
    }

    /** @test */
    public function returns404Response()
    {
        $request = new Request('GET', '/any/path');
        $errorController = new ErrorController($this->app, $request);

        self::assertSame(404, $errorController->notFound()->getStatusCode());
    }

    /** @test */
    public function rendersNotFoundError()
    {
        $request = new Request('GET', '/any/path');
        $errorController = new ErrorController($this->app, $request);

        $body = $errorController->notFound()->getBody()->getContents();

        self::assertStringContainsString('File Not Found', $body);
        self::assertStringContainsString('/any/path', $body);
    }

    /** @test */
    public function returns405Response()
    {
        $request = (new Request('POST', '/any/path'));
        $errorController = new ErrorController($this->app, $request);

        self::assertSame(405, $errorController->methodNotAllowed(['GET'])->getStatusCode());
    }

    /** @test */
    public function rendersMethodNotAllowed()
    {
        $request = (new Request('POST', '/any/path'));
        $errorController = new ErrorController($this->app, $request);

        $body = $errorController->methodNotAllowed(['GET'])->getBody()->getContents();

        self::assertStringContainsString('Method Not Allowed', $body);
        self::assertStringContainsString('/any/path', $body);
        self::assertStringContainsString('GET', $body);
    }
}

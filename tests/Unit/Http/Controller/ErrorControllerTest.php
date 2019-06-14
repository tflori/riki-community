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
        $errorController = new ErrorController('unexpectedError');
        $request = new Request('GET', '/any/path');

        self::assertSame(500, $errorController->handle($request)->getStatusCode());
    }

    /** @test */
    public function rendersUnexpectedError()
    {
        $errorController = new ErrorController('unexpectedError');
        $request = new Request('GET', '/any/path');

        $body = $errorController->handle($request)->getBody()->getContents();

        self::assertContains('Unexpected Error', $body);
    }

    /** @test */
    public function returnsJsonWhenPreferred()
    {
        $errorController = new ErrorController('unexpectedError');
        $request = new Request('GET', '/any/path', [
            'accept' => 'application/json',
        ]);

        $body = $errorController->handle($request)->getBody()->getContents();

        self::assertNotSame('null', $body);
        self::assertNotNull(json_decode($body));
    }

    /** @test */
    public function jsonContainsTheExceptionWhenAvailable()
    {
        $errorController = new ErrorController('unexpectedError');
        $request = (new Request('GET', '/any/path', [
            'accept' => 'application/json',
        ]))->withAttribute('arguments', [new InvalidArgumentException('This was expected')]);

        $body = $errorController->handle($request)->getBody()->getContents();
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
        $errorController = new ErrorController('notFound');
        $request = new Request('GET', '/any/path');

        self::assertSame(404, $errorController->handle($request)->getStatusCode());
    }

    /** @test */
    public function rendersNotFoundError()
    {
        $errorController = new ErrorController('notFound');
        $request = new Request('GET', '/any/path');

        $body = $errorController->handle($request)->getBody()->getContents();

        self::assertContains('File Not Found', $body);
        self::assertContains('/any/path', $body);
    }

    /** @test */
    public function returns405Response()
    {
        $errorController = new ErrorController('methodNotAllowed');
        $request = (new Request('POST', '/any/path'))
            ->withAttribute('arguments', ['allowedMethods' => ['GET']]);

        self::assertSame(405, $errorController->handle($request)->getStatusCode());
    }

    /** @test */
    public function rendersMethodNotAllowed()
    {
        $errorController = new ErrorController('methodNotAllowed');
        $request = (new Request('POST', '/any/path'))
            ->withAttribute('arguments', ['allowedMethods' => ['GET']]);

        $body = $errorController->handle($request)->getBody()->getContents();

        self::assertContains('Method Not Allowed', $body);
        self::assertContains('/any/path', $body);
        self::assertContains('GET', $body);
    }
}

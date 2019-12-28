<?php

namespace Test\Unit\Service\Exception;

use App\Service\Exception\LogHandler;
use Mockery as m;
use RuntimeException;
use Test\TestCase;
use Throwable;
use Whoops\Exception\Inspector;

class LogHandlerTest extends TestCase
{
    protected function prepareHandler(Throwable $exception = null): LogHandler
    {
        $exception = $exception ?? new RuntimeException('Test exception');

        $handler = new LogHandler();
        $handler->setException($exception);
        $handler->setInspector(new Inspector($exception));

        return $handler;
    }

    /** @test */
    public function writesToLog()
    {
        $handler = $this->prepareHandler();

        $this->mocks['logger']->shouldReceive('error')->with(m::type('string'))
            ->once();

        $handler->handle();
    }

    /** @test */
    public function messageContainsTheExceptionMessage()
    {
        $exceptionMessage = 'Test exception ' . md5(microtime());
        $handler = $this->prepareHandler(new RuntimeException($exceptionMessage));

        $this->mocks['logger']->shouldReceive('error')->with(m::type('string'))
            ->once()->andReturnUsing(function (string $message) use ($exceptionMessage) {
                self::assertContains($exceptionMessage, $message);
            });

        $handler->handle();
    }

    /** @test */
    public function messageContainsTheErrorCode()
    {
        $code = mt_rand(1, 1000);
        $handler = $this->prepareHandler(new RuntimeException('Test exception', $code));

        $this->mocks['logger']->shouldReceive('error')->with(m::type('string'))
            ->once()->andReturnUsing(function (string $message) use ($code) {
                self::assertContains((string)$code, $message);
            });

        $handler->handle();
    }

    /** @test */
    public function messageContainsFileAndLine()
    {
        $exception = new RuntimeException('Test exception');
        $file = str_replace($this->app->environment->getBasePath(), '', $exception->getFile());
        $handler = $this->prepareHandler($exception);

        $this->mocks['logger']->shouldReceive('error')->with(m::type('string'))
            ->once()->andReturnUsing(function (string $message) use ($file, $exception) {
                self::assertContains($file, $message);
                self::assertContains((string)$exception->getLine(), $message);
            });

        $handler->handle();
    }

    /** @test */
    public function replacesBasePathWithProjectPath()
    {
        $exception = new RuntimeException('Test Exception');
        $handler = $this->prepareHandler($exception);


        $this->mocks['config']->shouldReceive('env')->with('PROJECT_PATH')
            ->atLeast()->once()->andReturn('/project');

        $this->mocks['logger']->shouldReceive('error')->with(m::type('string'))
            ->once()->andReturnUsing(function (string $message) use ($exception) {
                $expected = '/project' . substr($exception->getFile(), strlen($this->app->getBasePath()));
                self::assertContains($expected, $message);
            });

        $handler->handle();
    }

    /** @test */
    public function messageContainsPreviousExceptions()
    {
        $innerException = new RuntimeException('Inner Exception', 23);
        $outerException = new RuntimeException('Outer Exception', 42, $innerException);
        $handler = $this->prepareHandler($outerException);

        $this->mocks['logger']->shouldReceive('error')->with(m::type('string'))
            ->once()->andReturnUsing(function (string $message) use ($innerException) {
                $messages = explode('Caused by', $message);
                self::assertCount(2, $messages);
                self::assertContains('Inner Exception', $messages[1]);
                self::assertContains('23', $messages[1]);
                self::assertContains((string)$innerException->getLine(), $messages[1]);
            });

        $handler->handle();
    }

    /** @test */
    public function writesTraceAsDebugMessage()
    {
        $handler = $this->prepareHandler();

        $this->mocks['logger']->shouldReceive('debug')->with(m::type('string'))
            ->once();

        $handler->handle();
    }
}

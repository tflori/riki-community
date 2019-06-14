<?php

namespace Test\Unit;

use App\Kernel;
use App\Service\Exception\ConsoleHandler;
use App\Service\Exception\LogHandler;
use Test\TestCase;
use Whoops\Handler\PlainTextHandler;
use Mockery as m;

class ApplicationTest extends TestCase
{
    /** @test */
    public function registersErrorHandler()
    {
         $this->mocks['whoops']->shouldReceive('register')->with()
           ->once()->andReturnSelf();

         $this->app->initWhoops();
    }

    /** @test */
    public function definesAnErrorHandlerForLogging()
    {
        $this->app->initWhoops();

        self::assertInstanceOf(LogHandler::class, $this->app->get('whoops')->popHandler());
    }

    /** @test */
    public function prependsAndRemovesHandlerFromKernel()
    {
        $handlersBefore = $this->app->whoops->getHandlers();
        $kernelHandlers = [new ConsoleHandler($this->app)];
        $kernel = m::mock(Kernel::class);
        $kernel->shouldReceive('getBootstrappers')->andReturn([]);
        $kernel->shouldReceive('getErrorHandlers')->with($this->app)
            ->once()->andReturn($kernelHandlers);


        $kernel->shouldReceive('handle')->with()
            ->once()->andReturnUsing(function () use ($handlersBefore, $kernelHandlers) {
                self::assertEquals(array_merge($kernelHandlers, $handlersBefore), $this->app->whoops->getHandlers());
            });

        $this->app->run($kernel);

        self::assertEquals($handlersBefore, $this->app->whoops->getHandlers());
    }
}

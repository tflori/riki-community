<?php

namespace Test\Unit;

use App\Application;
use App\Kernel;
use App\Service\Exception\ConsoleHandler;
use App\Service\Exception\LogHandler;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Whoops\Util\SystemFacade;

class ApplicationTest extends MockeryTestCase
{
    /** @var m\MockInterface|SystemFacade */
    protected $systemFacade;

    protected function setUp(): void
    {
        parent::setUp();

        $this->systemFacade = m::mock(SystemFacade::class)->shouldIgnoreMissing();
    }

    protected function tearDown()
    {
        parent::tearDown();
        Application::app()->destroy();
    }

    /** @test */
    public function registersErrorHandler()
    {
        /** @var m\MockInterface|Application $app */
        $app = m::mock(Application::class)->makePartial();
        $app->instance(SystemFacade::class, $this->systemFacade);

        $this->systemFacade->shouldReceive('setErrorHandler')->once();
        $this->systemFacade->shouldReceive('setExceptionHandler')->once();
        $this->systemFacade->shouldReceive('registerShutdownFunction')->once();

        $app->__construct('/app');
    }

    /** @test */
    public function definesAnErrorHandlerForLogging()
    {
        $app = new Application('/app');

        self::assertInstanceOf(LogHandler::class, $app->whoops->popHandler());
    }

    /** @test */
    public function prependsAndRemovesHandlerFromKernel()
    {
        $app = new Application('/app');

        $kernelHandler = new ConsoleHandler();
        $kernel = m::mock(Kernel::class);
        $kernel->shouldReceive('getBootstrappers')->andReturn([]);
        $kernel->shouldReceive('getErrorHandlers')->with()
            ->once()->andReturn([$kernelHandler]);

        $kernel->shouldReceive('handle')->with()
            ->once()->andReturnUsing(function () use ($kernelHandler, $app) {
                self::assertSame($kernelHandler, $app->whoops->getHandlers()[0]);
                return 0;
            });

        $app->run($kernel);

        self::assertInstanceOf(LogHandler::class, $app->whoops->popHandler());
    }
}

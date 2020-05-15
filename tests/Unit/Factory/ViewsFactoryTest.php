<?php

namespace Test\Unit\Factory;

use App\Factory\ViewsFactory;
use Syna\Factory;
use Syna\HelperLocator;
use Syna\ViewLocator;
use Test\TestCase;

class ViewsFactoryTest extends TestCase
{
    /** @test */
    public function returnsAFactory()
    {
        $factory = new ViewsFactory($this->app);

        self::assertInstanceOf(Factory::class, $factory->getInstance());
    }

    /** @test */
    public function addsViewPath()
    {
        $factory = new ViewsFactory($this->app);

        /** @var Factory $views */
        $views = $factory->getInstance();

        self::assertEquals(
            new ViewLocator($this->app->environment->resourcePath('views')),
            $views->getLocator()
        );
    }

    /** @test */
    public function addsHelperNamespace()
    {
        $factory = new ViewsFactory($this->app);

        /** @var Factory $views */
        $views = $factory->getInstance();

        self::assertEquals(
            new HelperLocator('App\View\Helper', [$this->app, 'get']),
            $views->getHelperLocator()
        );
    }

    /** @test */
    public function addsLayoutPath()
    {
        $factory = new ViewsFactory($this->app);

        /** @var Factory $views */
        $views = $factory->getInstance();

        self::assertEquals(
            new ViewLocator($this->app->environment->resourcePath('layouts')),
            $views->getLocator('layout')
        );
    }

    /** @test */
    public function addsMailPath()
    {
        $factory = new ViewsFactory($this->app);

        /** @var Factory $views */
        $views = $factory->getInstance();

        self::assertEquals(
            new ViewLocator($this->app->environment->resourcePath('mails'), '.md.php'),
            $views->getLocator('mail')
        );
    }
}

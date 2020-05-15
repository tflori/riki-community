<?php

namespace Test\Factory;

use Syna\Factory;
use Syna\HelperLocator;
use Syna\ViewLocator;
use Mockery as m;

class ViewsFactory extends \App\Factory\ViewsFactory
{
    /** @return Factory */
    protected function build()
    {
        $factory = m::mock(Factory::class, [
            new ViewLocator($this->container->environment->resourcePath('views')),
            new HelperLocator('App\View\Helper', [$this->container, 'get']),
            new ViewLocator($this->container->environment->resourcePath('layouts'))
        ])->makePartial();

        $factory->addLocator('mail', new ViewLocator($this->container->environment->resourcePath('mails'), '.md.php'));

        return $factory;
    }
}

<?php

namespace App\Factory;

use Syna\Factory;
use Syna\HelperLocator;
use Syna\ViewLocator;

class ViewsFactory extends AbstractFactory
{
    protected $shared = true;

    /** @return Factory */
    protected function build()
    {
        $factory =  new Factory(
            new ViewLocator($this->container->environment->resourcePath('views')),
            new HelperLocator('App\View\Helper', [$this->container, 'get']),
            new ViewLocator($this->container->environment->resourcePath('layouts'))
        );

        $factory->addLocator('mail', new ViewLocator($this->container->environment->resourcePath('mails'), '.md.php'));

        return $factory;
    }
}

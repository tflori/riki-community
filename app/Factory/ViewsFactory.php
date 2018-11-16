<?php

namespace App\Factory;

use Syna\Factory;
use Syna\HelperLocator;
use Syna\ViewLocator;

class ViewsFactory extends AbstractFactory
{
    protected $shared = true;

    /**
     * This method builds the instance.
     *
     * @return mixed
     */
    protected function build()
    {
        return new Factory(
            new ViewLocator($this->container->environment->resourcePath('views')),
            new HelperLocator('App\View\Helper'),
            new ViewLocator($this->container->environment->resourcePath('layouts'))
        );
    }
}

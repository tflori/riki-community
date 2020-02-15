<?php

namespace App\Factory;

use App\Service\Authorization;

class AuthFactory extends AbstractFactory
{
    protected $shared = true;

    protected function build()
    {
        return new Authorization($this->container);
    }
}

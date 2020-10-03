<?php

namespace App\Factory;

use App\Service\Url;

class UrlFactory extends AbstractFactory
{
    protected $shared = true;

    /** @codeCoverageIgnore trivial */
    protected function build()
    {
        return new Url(
            $this->container->config->fallbackUrl,
            $this->container->has('request') ? $this->container->request : null
        );
    }
}

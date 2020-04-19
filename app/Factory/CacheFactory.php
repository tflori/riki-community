<?php

namespace App\Factory;

use App\Service\Cache;
use Symfony\Component\Cache\Adapter\RedisAdapter;

/** @codeCoverageIgnore  */
class CacheFactory extends AbstractFactory
{
    protected $shared = true;

    protected function build()
    {
        return new Cache(new RedisAdapter($this->container->redis));
    }
}

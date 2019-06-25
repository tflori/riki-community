<?php

namespace App\Factory;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Psr16Cache;

/** @codeCoverageIgnore  */
class CacheFactory extends AbstractFactory
{
    protected $shared = true;

    protected function build()
    {
        $cache = new Psr16Cache(new RedisAdapter($this->container->redis));
        return $cache;
    }
}

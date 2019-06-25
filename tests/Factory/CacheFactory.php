<?php

namespace Test\Factory;

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;

class CacheFactory extends \App\Factory\CacheFactory
{
    protected function build()
    {
        return new Psr16Cache(new ArrayAdapter());
    }
}

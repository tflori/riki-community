<?php

namespace App\Factory;

use Redis;

/** @codeCoverageIgnore  */
class RedisFactory extends AbstractFactory
{
    protected $shared = true;

    protected function build()
    {
        $config = $this->container->config->cacheConfig;
        $redis = new Redis();
        $redis->connect($config->host, $config->port, 1);
        $redis->select($config->dbNumber);
        $redis->setOption(Redis::OPT_PREFIX, $config->prefix);
        $redis->ping();
        return $redis;
    }
}

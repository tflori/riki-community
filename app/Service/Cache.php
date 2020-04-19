<?php

namespace App\Service;

use Symfony\Component\Cache\Psr16Cache;

class Cache extends Psr16Cache
{
    public function remember($key, callable $getter, $ttl = null)
    {
        if (!$this->has($key)) {
            $this->set($key, $value = $getter(), $ttl);
            return $value;
        }

        return $this->get($key);
    }
}

<?php

namespace Test\Unit\Service;

use Mockery as m;
use Test\TestCase;

class CacheTest extends TestCase
{
    /** @test */
    public function rememberCallsTheCallbackWhenNotCached()
    {
        $spy = m::spy(function () {
            return 'bar';
        });

        $this->app->cache->remember('foo', $spy);

        $spy->shouldHaveBeenCalled()->with()->once();
    }

    /** @test */
    public function rememberStoresTheReturnedValueInCache()
    {
        $spy = m::spy(function () {
            return 'bar';
        });

        $this->app->cache->remember('foo', $spy);

        self::assertTrue($this->app->cache->has('foo'), 'Failed asserting that the cache has the key "foo".');
    }

    /** @test */
    public function rememberIsNotExecutingTheCallbackWhenCached()
    {
        $this->app->cache->set('foo', 'baz');
        $spy = m::spy(function () {
            return 'bar';
        });

        $this->app->cache->remember('foo', $spy);

        $spy->shouldNotHaveBeenCalled();
    }
}

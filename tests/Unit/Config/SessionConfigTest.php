<?php

namespace Test\Unit\Config;

use App\Config\SessionConfig;
use function GuzzleHttp\Psr7\build_query;
use Test\TestCase;

class SessionConfigTest extends TestCase
{
    /** @test */
    public function savePathIsATcpUrl()
    {
        $config = new SessionConfig('session', 'localhost', '1234');

        $path = $config->getSavePath();

        self::assertStringStartsWith('tcp://localhost:1234', $path);
    }

    /** @test */
    public function savePathContainsAPrefix()
    {
        $config = new SessionConfig('a_sess', 'localhost');

        $path = $config->getSavePath();

        self::assertContains(http_build_query([
            'prefix' => 'a_sess:'
        ]), $path);
    }

    /** @test */
    public function savePathContainsTheDatabase()
    {
        $config = new SessionConfig('session', 'localhost', 6379, 2);

        $path = $config->getSavePath();

        self::assertContains(http_build_query([
            'database' => 2
        ]), $path);
    }
}

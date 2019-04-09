<?php

namespace Test\Unit\View\Helper;

use App\View\Helper\CacheBuster;
use Test\TestCase;

class CacheBusterTest extends TestCase
{
    /** @test */
    public function appendsMd5ToAFileInPublicPath()
    {
        $cacheBuster = new CacheBuster();
        $md5 = md5_file($this->mocks['environment']->publicPath('/favicon.ico'));

        $uri = $cacheBuster('/favicon.ico');

        self::assertSame('/favicon.ico?_=' . substr($md5, 0, 10), $uri);
    }

    /** @test */
    public function returnsThePathWithoutCacheBuster()
    {
        $cacheBuster = new CacheBuster();

        $uri = $cacheBuster('/any/filePath');

        self::assertSame('/any/filePath', $uri);
    }
}

<?php

namespace Test\Http\Home;

use Test\Http\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function rendersAFullPage()
    {
        $result = $this->get('/home');

        self::assertStringContainsString('<header', (string)$result->getBody());
        self::assertStringContainsString('<footer', (string)$result->getBody());
    }
}

<?php

namespace Test\Http\Home;

use Test\Http\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function rendersAFullPage()
    {
        $result = $this->get('/home');

        self::assertContains('<header', (string)$result->getBody());
        self::assertContains('<footer', (string)$result->getBody());
    }
}

<?php

namespace Test\Http\Auth;

use Test\Http\TestCase;

class CsrfTokenTest extends TestCase
{
    /** @test */
    public function returnsAJsonEncodedString()
    {
        $response = $this->get('/auth/token');

        self::assertJson($response->getBody());
        self::assertIsString(json_decode($response->getBody()));
    }
}

<?php

namespace Test\Unit\Http\Controller;

use App\Http\Controller\AuthController;
use App\Model\Request;
use Test\TestCase;

class AuthControllerTest extends TestCase
{
    /** @test */
    public function returnsTheCurrentUser()
    {
        $user = $this->signIn();
        $controller = new AuthController($this->app, new Request('GET', '/auth'));

        $response = $controller->getUser();

        self::assertJson($response->getBody());
        self::assertEquals(json_encode($user), (string)$response->getBody());
    }
}

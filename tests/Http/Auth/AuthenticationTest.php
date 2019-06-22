<?php

namespace Test\Http\Auth;

use Community\Model\User;
use Test\Http\TestCase;

class AuthenticationTest extends TestCase
{
    /** @test */
    public function returnsCurrentAuthenticatedUser()
    {
        $user = $this->signIn();

        $response = $this->get('/auth');

        self::assertJson($response->getBody());
        self::assertSame(json_encode($user), (string)$response->getBody());
    }

    /** @test */
    public function returnsAuthenticatedUser()
    {
        $user = new User([
            'email' => 'john.doe@example.com',
            'password' => password_hash('asdf123', PASSWORD_BCRYPT, ['cost' => 4]),
            'created' => date('c'),
            'updated' => date('c'),
        ]);
        $this->expectFetch(User::class, ['/email"?\s*=\s*\'john.doe@example.com\'/'], $user);

        $response = $this->post('/auth', [], json_encode([
            'email' => 'john.doe@example.com',
            'password' => 'asdf123',
        ]));

        self::assertJson($response->getBody());
        self::assertSame(json_encode($user), (string)$response->getBody());
    }

    /** @test */
    public function staysLoggedInViaSession()
    {
        $user = new User([
            'email' => 'john.doe@example.com',
            'password' => password_hash('asdf123', PASSWORD_BCRYPT, ['cost' => 4]),
            'created' => date('c'),
            'updated' => date('c'),
        ]);
        $this->expectFetch(User::class, ['/email"?\s*=\s*\'john.doe@example.com\'/'], $user);
        $this->post('/auth', [], json_encode([
            'email' => 'john.doe@example.com',
            'password' => 'asdf123',
        ]));

        $response = $this->get('/auth');

        self::assertJson($response->getBody());
        self::assertSame(json_encode($user), (string)$response->getBody());
    }
}

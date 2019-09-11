<?php

namespace Test\Http\Auth;

use Community\Model\Token\AbstractToken;
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
        $this->ormAddResult(User::class, $user)
            ->where('email', 'john.doe@example.com');

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
        $this->ormAddResult(User::class, $user)
            ->where('email', 'john.doe@example.com');
        $this->post('/auth', [], json_encode([
            'email' => 'john.doe@example.com',
            'password' => 'asdf123',
        ]));

        $response = $this->get('/auth');

        self::assertJson($response->getBody());
        self::assertSame(json_encode($user), (string)$response->getBody());
    }

    /** @test */
    public function removesAuthentication()
    {
        $this->signIn();
        $token = json_decode($this->get('/auth/token')->getBody());

        $this->call('delete', '/auth', ['csrf_token' => $token]);

        $response = $this->get('/auth');
        self::assertSame('null', (string)$response->getBody());
    }

    /** @test */
    public function staysLoggedInWithoutToken()
    {
        $user = $this->signIn();

        $this->call('delete', '/auth');

        $response = $this->get('/auth');
        self::assertJson($response->getBody());
        self::assertSame(json_encode($user), (string)$response->getBody());
    }

    /** @test */
    public function staysLoggedInWithWrongToken()
    {
        $user = $this->signIn();
        $this->app->session->set('csrfToken', $token = AbstractToken::generateToken(10));

        $this->call('delete', '/auth', ['csrf_token' => $token . 'foo']);

        $response = $this->get('/auth');
        self::assertJson($response->getBody());
        self::assertSame(json_encode($user), (string)$response->getBody());
    }
}

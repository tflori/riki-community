<?php

namespace Test\Unit\Http\Controller;

use App\Http\Controller\AuthController;
use App\Http\HttpKernel;
use App\Model\Request;
use Community\Model\User;
use Test\TestCase;

class AuthControllerTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->ormAddResult(User::class, new User([
            'email' => 'arthur.dent@example.com',
            'password' => 'foo123',
            'display_name' => 'arthur',
        ]), new User([
            'email' => 'ford.prefect@example.com',
            'password' => '123foo',
            'display_name' => 'ford',
        ]));
    }

    /** @test */
    public function requestsTheUserByEmail()
    {
        $request = new Request('POST', '/auth', [], json_encode([
            'email' => 'john.doe@example.com',
            'password' => 'foo123',
        ]));
        $this->ormAddResult(User::class)->where('email', 'john.doe@example.com');

        $controller = new AuthController($this->app, $request);
        $controller->authenticate();
    }

    /** @test */
    public function returnsBadRequestErrorWhenUserNotFound()
    {
        $request = new Request('POST', '/auth', [], json_encode([
            'email' => 'john.doe@example.com',
            'password' => 'foo123',
        ]));
        $this->ormAddResult(User::class)->where('email', 'john.doe@example.com');
        $controller = new AuthController($this->app, $request);

        $response = $controller->authenticate();

        self::assertSame(400, $response->getStatusCode());
    }

    /** @test */
    public function returnsBadRequestWhenPasswordDoesNotMatch()
    {
        $request = new Request('POST', '/auth', [], json_encode([
            'email' => 'john.doe@example.com',
            'password' => 'foo123',
        ]));
        $user = new User([
            'email' => 'john.doe@example.com',
            'password' => password_hash('foo bar', PASSWORD_BCRYPT, ['cost' => 4]),
            'created' => date('c'),
            'updated' => date('c'),
        ]);
        $this->ormAddResult(User::class, $user)->where('email', 'john.doe@example.com');
        $controller = new AuthController($this->app, $request);

        $response = $controller->authenticate();

        self::assertSame(400, $response->getStatusCode());
    }

    /** @test */
    public function storesAuthRequestsWithInvalidEmail()
    {
        $request = new Request('POST', '/auth', [], json_encode([
            'email' => 'john.doe@example.com',
            'password' => 'foo123',
        ]), 1.1, [
            'REMOTE_ADDR' => '172.19.0.9',
        ]);
        $this->ormAddResult(User::class)->where('email', 'john.doe@example.com');
        $controller = new AuthController($this->app, $request);
        $key = sprintf(AuthController::LOGIN_ATTEMPTS_KEY, 'ip', $request->getIp());

        $controller->authenticate();

        self::assertIsArray($this->app->cache->get($key));
        $authRequests = $this->app->cache->get($key);
        self::assertEquals(time(), array_pop($authRequests), '', 1);
    }

    /** @dataProvider provideAuthAttemptsPerIp
     * @param array $attempts
     * @param bool  $blocked
     * @test */
    public function deniesFurtherTriesWhenLimitReachedForIp(array $attempts, bool $blocked)
    {
        $request = new Request('POST', '/auth', [], json_encode([
            'email' => 'john.doe@example.com',
            'password' => 'foo123',
        ]), 1.1, [
            'REMOTE_ADDR' => '172.19.0.9',
        ]);
        $controller = new AuthController($this->app, $request);
        $key = sprintf(AuthController::LOGIN_ATTEMPTS_KEY, 'ip', $request->getIp());

        $this->app->cache->set($key, array_map(function ($seconds) {
            return  time() - $seconds;
        }, $attempts));

        $response = $controller->authenticate();

        $blocked ?
            self::assertSame(423, $response->getStatusCode()) :
            self::assertSame(400, $response->getStatusCode());
    }

    /** @test */
    public function storesAuthRequestsWithInvalidPassword()
    {
        $request = new Request('POST', '/auth', [], json_encode([
            'email' => 'john.doe@example.com',
            'password' => 'foo123',
        ]), 1.1, [
            'REMOTE_ADDR' => '172.19.0.9',
        ]);
        $user = new User([
            'id' => rand(1000000, 2000000),
            'email' => 'john.doe@example.com',
            'password' => password_hash('foo bar', PASSWORD_BCRYPT, ['cost' => 4]),
            'created' => date('c'),
            'updated' => date('c'),
        ]);
        $this->ormAddResult(User::class, $user)->where('email', 'john.doe@example.com');
        $controller = new AuthController($this->app, $request);
        $key = sprintf(AuthController::LOGIN_ATTEMPTS_KEY, 'user', $user->id);

        $controller->authenticate();

        self::assertIsArray($this->app->cache->get($key));
        $authRequests = $this->app->cache->get($key);
        self::assertEquals(time(), array_pop($authRequests), '', 1);
    }

    /** @dataProvider provideAuthAttemptsPerUser
     * @param array $attempts
     * @param bool  $blocked
     * @test */
    public function deniesFurtherTriesWhenLimitReachedForUser(array $attempts, bool $blocked)
    {
        $request = new Request('POST', '/auth', [], json_encode([
            'email' => 'john.doe@example.com',
            'password' => 'foo123',
        ]), 1.1, [
            'REMOTE_ADDR' => '172.19.0.9',
        ]);
        $user = new User([
            'id' => rand(1000000, 2000000),
            'email' => 'john.doe@example.com',
            'password' => password_hash('foo bar', PASSWORD_BCRYPT, ['cost' => 4]),
            'created' => date('c'),
            'updated' => date('c'),
        ]);
        $this->ormAddResult(User::class, $user)->where('email', 'john.doe@example.com');
        $controller = new AuthController($this->app, $request);
        $key = sprintf(AuthController::LOGIN_ATTEMPTS_KEY, 'user', $user->id);

        $this->app->cache->set($key, array_map(function ($seconds) {
            return  time() - $seconds;
        }, $attempts));

        $response = $controller->authenticate();

        $blocked ?
            self::assertSame(423, $response->getStatusCode()) :
            self::assertSame(400, $response->getStatusCode());
    }

    public function provideAuthAttemptsPerIp()
    {
        $result = [];
        $last = 0;
        foreach (AuthController::LOGIN_ATTEMPTS_LIMITS['ip'] as $seconds => $limit) {
            $result[] = [array_fill(0, $limit, $seconds-1), true]; // reached $limit within $seconds => blocked
            $result[] = [array_fill(0, $limit, $seconds+1), false]; // all are older => not blocked
            $result[] = [array_fill(0, $limit-1, $seconds-1), false]; // limit not reached => not blocked
            $result[] = [
                array_fill(0, 1, $last-1) + array_fill(1, $limit-1, $seconds-1),
                true, // one within last limit-1 within this time range => blocked
            ];
            $last = $seconds;
        }
        return $result;
    }

    public function provideAuthAttemptsPerUser()
    {
        $result = [];
        $last = 0;
        foreach (AuthController::LOGIN_ATTEMPTS_LIMITS['user'] as $seconds => $limit) {
            $result[] = [array_fill(0, $limit, $seconds-1), true]; // reached $limit within $seconds => blocked
            $result[] = [array_fill(0, $limit, $seconds+1), false]; // all are older => not blocked
            $result[] = [array_fill(0, $limit-1, $seconds-1), false]; // limit not reached => not blocked
            $result[] = [
                array_fill(0, 1, $last-1) + array_fill(1, $limit-1, $seconds-1),
                true, // one within last limit-1 within this time range => blocked
            ];
            $last = $seconds;
        }
        return $result;
    }
}

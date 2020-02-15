<?php

namespace Test\Unit\Service;

use Community\Model\User;
use Test\TestCase;

class AuthorizationTest extends TestCase
{
    /** @test */
    public function fetchesTheUserFromDatabase()
    {
        $user = $this->ormCreateMockedEntity(User::class);
        $this->app->session->set('userId', $user->id);

        $this->mocks['entityManager']->shouldReceive('fetch')
            ->with(User::class, $user->id)
            ->once()->andReturn($user);

        $result = $this->app->auth->getUser();

        self::assertSame($user, $result);
    }
}

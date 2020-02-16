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

    /** @test */
    public function getsThePermissionsFromCurrentUser()
    {
        $user = $this->signIn();

        $user->shouldReceive('getAllPermissions')->with()
            ->once()->andReturn([
                'user:edit' => true,
                'user:delete' => false,
            ]);

        self::assertSame([
            'user:edit' => true,
            'user:delete' => false,
        ], $this->app->auth->getPermissions());
    }

    /** @test */
    public function restrictedPermissionsAreNotGranted()
    {
        $user = $this->signIn();

        $user->shouldReceive('getAllPermissions')->with()
            ->once()->andReturn([
                'user:edit' => true,
                'user:delete' => false,
            ]);

        self::assertFalse($this->app->auth->hasPermission('user:delete'));
    }
}

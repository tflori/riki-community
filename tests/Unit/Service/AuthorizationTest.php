<?php

namespace Test\Unit\Service;

use Community\Model\User;
use ORM\Event\Updated;
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
    public function getsTheUserFromCache()
    {
        $user = new User();
        $this->em->addEntity($user);
        $this->app->session->set('userId', $user->id);
        $this->app->cache->set('user-' . $user->id, $user);

        $this->mocks['entityManager']->shouldNotReceive('fetch');

        $result = $this->app->auth->getUser();

        self::assertEquals($user, $result);
    }

    /** @test */
    public function whenUserGetsUpdatedTheCacheWillBeInvalidated()
    {
        $user = new User(['email' => 'john.doe@example.com']);
        $this->em->addEntity($user);
        $this->app->cache->set('user-' . $user->id, $user);

        $this->em->fire(new Updated($user, ['email' => ['john.doe@example.com', 'jdoe@example.com']]));

        self::assertFalse($this->app->cache->has('user-' . $user->id));
    }
}

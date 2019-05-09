<?php

namespace Test\Unit\Community\Model\Concerns;

use Community\Model\Concerns\WithUpdated;
use Community\Model\User;
use DateTime;
use Mockery as m;
use Test\TestCase;

class WithUpdatedTest extends TestCase
{
    /** @test */
    public function returnsDateTimeObject()
    {
        $this->requiresTrait(User::class, WithUpdated::class);
        $user = new User(['updated' => '2016-05-23 23:42:17.123']);

        $result = $user->updated;

        self::assertInstanceOf(DateTime::class, $result);
    }

    /** @test */
    public function returnsNullWhenNoDataExists()
    {
        $this->requiresTrait(User::class, WithUpdated::class);
        $user = new User();

        $result = $user->updated;

        self::assertNull($result);
    }

    /** @test */
    public function storesNowWithMicroseconds()
    {
        $this->requiresTrait(User::class, WithUpdated::class);
        $user = new User();

        $user->setUpdated();

        self::assertEquals(microtime(true), (double)$user->updated->format('U.u'), '', 0.01);
    }

    /** @test */
    public function setsUpdatedPreUpdate()
    {
        $this->requiresTrait(User::class, WithUpdated::class);
        /** @var m\Mock|User $user */
        $user = m::mock(User::class)->makePartial();
        $user->shouldReceive('setUpdated')->with()->once();

        $user->preUpdate();
    }

    /** @test */
    public function returnsNullWhenNoEntity()
    {
        $noEntity = new NoEntity();

        $result = $noEntity->getUpdated();

        self::assertNull($result);
    }

    /** @test */
    public function doesNothingWhenNoEntity()
    {
        $noEntity = new NoEntity();

        $noEntity->setUpdated();

        self::assertTrue(true);
    }
}

<?php

namespace Test\Unit\Community\Model\Concerns;

use Community\Model\Concerns\WithCreated;
use Community\Model\User;
use DateTime;
use Test\TestCase;

class WithCreatedTest extends TestCase
{
    /** @test */
    public function returnsDateTimeObject()
    {
        $this->requiresTrait(User::class, WithCreated::class);
        $user = new User(['created' => '2016-05-23 23:42:17.123']);

        $result = $user->created;

        self::assertInstanceOf(DateTime::class, $result);
    }

    /** @test */
    public function returnsNullWhenNoDataExists()
    {
        $this->requiresTrait(User::class, WithCreated::class);
        $user = new User();

        $result = $user->created;

        self::assertNull($result);
    }

    /** @test */
    public function storesNowWithMicroseconds()
    {
        $this->requiresTrait(User::class, WithCreated::class);
        $user = new User();

        $user->setCreated();

        self::assertEquals(microtime(true), (double)$user->created->format('U.u'), '', 0.01);
    }

    /** @test */
    public function doesNotOverwriteExisting()
    {
        $this->requiresTrait(User::class, WithCreated::class);
        $user = new User(['created' => '2016-05-23 23:42:17.123']);

        $user->setCreated(new DateTime());

        self::assertSame('2016-05-23 23:42:17.123000', $user->created->format('Y-m-d H:i:s.u'));
    }

    /** @test */
    public function returnsNullWhenNoEntity()
    {
        $noEntity = new NoEntity();

        $result = $noEntity->getCreated();

        self::assertNull($result);
    }

    /** @test */
    public function doesNothingWhenNoEntity()
    {
        $noEntity = new NoEntity();

        $noEntity->setCreated();

        self::assertTrue(true);
    }
}

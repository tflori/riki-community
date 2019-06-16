<?php

namespace Test\Unit\Factory;

use App\Factory\GateFactory;
use App\Model\Gate;
use Test\TestCase;
use Verja\Field;

class GateFactoryTest extends TestCase
{
    /** @test */
    public function setsAcceptedFieldsAndMessages()
    {
        $factory = new GateFactory($this->app);

        /** @var Gate $gate */
        $gate = $factory->getInstance(['foo'], ['foo' => 'bar']);

        self::assertEquals(['foo' => new Field()], self::getProtectedProperty($gate, 'fields'));
        self::assertEquals(['foo' => 'bar'], $gate->getMessages());
    }
}

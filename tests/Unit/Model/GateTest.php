<?php

namespace Test\Unit\Model;

use App\Application;
use App\Http\HttpKernel;
use App\Model\Gate;
use Carbon\Carbon;
use Mockery as m;
use Test\Example\ResponseCreator;
use Test\TestCase;
use Verja\Error;

class GateTest extends TestCase
{
    /** @test */
    public function returnsTheMessagesFromErrors()
    {
        /** @var Gate|m\Mock $gate */
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('getErrors')->with()
            ->once()->andReturn([
                'foo' => [new Error('NO_MATCH', 'bar', 'does not match')],
            ]);

        $messages = $gate->getErrorMessages();

        self::assertSame(['foo' => ['Does not match']], $messages);
    }

    /** @test */
    public function returnsTheProvidedMessage()
    {
        /** @var Gate|m\Mock $gate */
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('getErrors')->with()
            ->once()->andReturn([
                'foo' => [new Error('NO_MATCH', 'bar', 'does not match')],
            ]);

        $messages = $gate->getErrorMessages(['NO_MATCH' => 'this should match']);

        self::assertSame(['foo' => ['this should match']], $messages);
    }

    /** @test */
    public function prefersSpecificMessages()
    {
        /** @var Gate|m\Mock $gate */
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('getErrors')->with()
            ->once()->andReturn([
                'foo' => [new Error('NO_MATCH', 'bar', 'does not match')],
            ]);

        $messages = $gate->getErrorMessages([
            'NO_MATCH' => 'this should match',
            'foo.NO_MATCH' => 'Foo does not match',
        ]);

        self::assertSame(['foo' => ['Foo does not match']], $messages);
    }

    /** @test */
    public function replacesPropertiesInMessages()
    {
        /** @var Gate|m\Mock $gate */
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('getErrors')->with()
            ->once()->andReturn([
                'foo' => [new Error('NOT_IN', 'bar', 'does not match', [
                    'values' => 'a,b,c'
                ])],
            ]);

        $messages = $gate->getErrorMessages([
            'NOT_IN' => '%value$s has to be one of %values$s',
        ]);

        self::assertSame(['foo' => ['bar has to be one of a,b,c']], $messages);
    }

    /** @test */
    public function removesPlaceholdersForUndefinedProperties()
    {
        /** @var Gate|m\Mock $gate */
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('getErrors')->with()
            ->once()->andReturn([
                'foo' => [new Error('NOT_IN', 'bar', 'does not match')],
            ]);

        $messages = $gate->getErrorMessages([
            'NOT_IN' => 'this is %bar$s',
        ]);

        self::assertSame(['foo' => ['this is ']], $messages);
    }

    /** @dataProvider provideNonScalarProperties
     * @param $property
     * @param $expected
     * @test */
    public function convertsNonScalarProperties($property, $expected)
    {
        /** @var Gate|m\Mock $gate */
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('getErrors')->with()
            ->once()->andReturn([
                'foo' => [new Error('ANY', 'bar', null, [
                    'property' => $property,
                ])],
            ]);

        $messages = $gate->getErrorMessages([
            'ANY' => '%property$s',
        ]);

        self::assertSame(['foo' => [$expected]], $messages);
    }

    public function provideNonScalarProperties()
    {
        return [
            [$carbon = new Carbon(), $carbon->__toString()],
            [['a', 'b', 'c'], 'a,b,c'],
            [['a' => 1, 'b' => '2', 123], '{"a":1,"b":"2","0":123}'],
            [new ResponseCreator(), ResponseCreator::class],
            [fopen('php://temp', 'w'), 'resource'],
        ];
    }
}

<?php

namespace Test\Cli\Db;

use GetOpt\ArgumentException\Invalid;
use Seeder\PermissionSeeder;
use Seeder\ProductionSeeder;
use Test\Cli\TestCase;

class SeedTest extends TestCase
{
    /** @test */
    public function createsTheSeeder()
    {
        $this->app->shouldReceive('make')->with(PermissionSeeder::class, $this->mocks['entityManager'])
            ->once()->andReturn($seeder = \Mockery::mock(PermissionSeeder::class)->makePartial());

        $this->start('db:seed', 'permission');
    }

    /** @test */
    public function executesTheSeeder()
    {
        $this->app->shouldReceive('make')->with(PermissionSeeder::class, $this->mocks['entityManager'])
            ->andReturn($seeder = \Mockery::mock(PermissionSeeder::class)->makePartial());
        $seeder->shouldReceive('sprout')->with()
            ->once();

        $this->start('db:seed', 'permission');
    }

    /** @test */
    public function executesTheProductionSeederByDefault()
    {
        $this->app->shouldReceive('make')->with(ProductionSeeder::class, $this->mocks['entityManager'])
            ->once()->andReturn($seeder = \Mockery::mock(ProductionSeeder::class)->makePartial());
        $seeder->shouldReceive('sprout')->with()
            ->once();

        $this->start('db:seed');
    }

    /** @test */
    public function throwsWhenTheSeederDoesNotExist()
    {
        self::expectException(Invalid::class);
        self::expectExceptionMessage('\\UnknownSeeder');

        $this->start('db:seed', 'unknown');
    }
}

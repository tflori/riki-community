<?php

namespace Test\Unit\Cli\Command\Db;

use App\Cli\Command\Db\BreytaCommand;
use App\Cli\Command\Db\Migrate;
use Breyta\Migrations;
use GetOpt\GetOpt;
use Test\TestCase;
use Mockery as m;

class MigrateTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->mocks['breyta'] = m::mock(Migrations::class);
        $this->mocks['breyta']->shouldReceive('setProgress')->with(m::type(BreytaCommand::class))->once();
        $this->app->instance(Migrations::class, $this->mocks['breyta']);
    }

    /** @test */
    public function executesMigrate()
    {
        $command = new Migrate($this->app, $this->mocks['console']);

        $this->mocks['breyta']->shouldReceive('migrate')->with()
            ->andReturn(true)->once();

        $command->handle(new GetOpt());
    }

    /** @test */
    public function executesMigrateTo()
    {
        $command = new Migrate($this->app, $this->mocks['console']);
        $getOpt = new GetOpt();
        $getOpt->addCommand($command);
        $getOpt->process('db:migrate 2018-04-23');

        $this->mocks['breyta']->shouldReceive('migrateTo')->with('2018-04-23')
            ->andReturn(true)->once();

        $command->handle($getOpt);
    }
}

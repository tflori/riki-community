<?php

namespace Test\Unit\Cli\Command\Db;

use App\Cli\Command\Db\BreytaCommand;
use App\Cli\Command\Db\Revert;
use Breyta\Migrations;
use GetOpt\GetOpt;
use Test\TestCase;
use Mockery as m;

class RevertTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mocks['breyta'] = m::mock(Migrations::class);
        $this->mocks['breyta']->shouldReceive('setProgress')->with(m::type(BreytaCommand::class))->once();
        $this->app->instance(Migrations::class, $this->mocks['breyta']);
    }

    /** @test */
    public function executesRevert()
    {
        $command = new Revert($this->app, $this->mocks['console']);

        $this->mocks['breyta']->shouldReceive('revert')->with()
            ->andReturn(true)->once();

        $command->handle(new GetOpt());
    }

    /** @test */
    public function executesRevertTo()
    {
        $command = new Revert($this->app, $this->mocks['console']);
        $getOpt = new GetOpt();
        $getOpt->addCommand($command);
        $getOpt->process('db:revert 2018-04-23');

        $this->mocks['breyta']->shouldReceive('revertTo')->with('2018-04-23')
            ->andReturn(true)->once();

        $command->handle($getOpt);
    }
}

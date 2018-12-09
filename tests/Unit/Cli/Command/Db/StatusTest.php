<?php

namespace Test\Unit\Cli\Command\Db;

use App\Cli\Command\Db\BreytaCommand;
use App\Cli\Command\Db\Status;
use Breyta\Migrations;
use Breyta\Model\Migration;
use GetOpt\Command;
use GetOpt\GetOpt;
use Hugga\Console;
use Test\TestCase;
use Mockery as m;

class StatusTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->mocks['breyta'] = m::mock(Migrations::class);
        $this->mocks['breyta']->shouldReceive('setProgress')->with(m::type(BreytaCommand::class))->once();
        $this->app->instance(Migrations::class, $this->mocks['breyta']);
    }

    /** @test */
    public function getsStatusFromBreyta()
    {
        $command = new Status($this->app, $this->mocks['console']);
        $getOpt = $this->buildGetopt($command, 'db:status');

        $this->mocks['breyta']->shouldReceive('getStatus')->with()
            ->once()->andReturn($this->buildStatus());

        $command->handle($getOpt);
    }

    /** @test */
    public function outputsInfoWhenNoMigrationsLeft()
    {
        $command = new Status($this->app, $this->mocks['console']);
        $getOpt = $this->buildGetopt($command, 'db:status');

        $this->mocks['breyta']->shouldReceive('getStatus')->with()
            ->once()->andReturn($this->buildStatus(Migration::createInstance([
                'file' => 'any/migration.php',
                'status' => 'done',
                'executed' => new \DateTime('-1 hour'),
                'execution_time' => 0.23
            ])));
        $this->mocks['console']->shouldReceive('info')->with('No migrations need to be applied.')
            ->once();

        $command->handle($getOpt);
    }

    /** @test */
    public function outputsAWarningWhenMigrationsLeft()
    {
        $command = new Status($this->app, $this->mocks['console']);
        $getOpt = $this->buildGetopt($command, 'db:status');

        $this->mocks['breyta']->shouldReceive('getStatus')->with()
            ->once()->andReturn($this->buildStatus(Migration::createInstance([
                'file' => 'any/migration.php',
                'status' => 'new',
            ])));
        $this->mocks['console']->shouldReceive('warn')->with('1 migration need to be applied.')
            ->once();

        $command->handle($getOpt);
    }

    /** @test */
    public function returnsTruthfulExitStatus()
    {
        $command = new Status($this->app, $this->mocks['console']);
        $getOpt = $this->buildGetopt($command, 'db:status --has-migrations');

        $this->mocks['breyta']->shouldReceive('getStatus')->with()
            ->once()->andReturn($this->buildStatus(Migration::createInstance([
                'file' => 'any/migration.php',
                'status' => 'new',
            ])));
        $this->mocks['console']->shouldNotReceive('write');

        $exitStatus = $command->handle($getOpt);

        self::assertSame(0, $exitStatus);
    }

    /** @test */
    public function outputsATableWithAllMigrations()
    {
        $command = new Status($this->app, $this->mocks['console']);
        $getOpt = $this->buildGetopt($command, 'db:status');

        $this->mocks['breyta']->shouldReceive('getStatus')->with()
            ->once()->andReturn($this->buildStatus(Migration::createInstance([
                'file' => '@breyta/CreateMigrationTable.php',
                'status' => 'done',
                'executed' => new \DateTime('2018-08-23T12:23:50Z'),
                'execution_time' => 0.06
            ]), Migration::createInstance([
                'file' => '2018-09-01T06:23:44Z_CreateUsersTable.php',
                'status' => 'done',
                'executed' => new \DateTime('2018-09-30T06:32:40Z'),
                'reverted' => new \DateTime('2018-09-22T20:06:12Z'),
                'execution_time' => 0.03,
            ]), Migration::createInstance([
                'file' => '2018-09-05T07:20:13Z_WhatEver.php',
                'status' => 'failed',
                'executed' => new \DateTime('2018-12-05T12:50:34Z'),
                'reverted' => '',
                'execution_time' => 0.021546,
            ]), Migration::createInstance([
                'file' => '2018-12-05T13:00:00Z_CreateUsersTable.php',
                'status' => 'new'
            ])));
        $this->mocks['console']->shouldReceive('line')->with(m::type('string'))
            ->once()->andReturnUsing(function ($table) {
                self::assertSame(
                    trim('
╭───────────────────────────────────────────┬────────┬─────────────────────┬─────────────────────┬────────────────╮
│ Name                                      │ Status │ Executed            │ Reverted            │ Execution Time │
├───────────────────────────────────────────┼────────┼─────────────────────┼─────────────────────┼────────────────┤
│ @breyta/CreateMigrationTable.php          │  done  │ 2018-08-23 12:23:50 │ -                   │           0.06 │
│ 2018-09-01T06:23:44Z_CreateUsersTable.php │  done  │ 2018-09-30 06:32:40 │ 2018-09-22 20:06:12 │           0.03 │
│ 2018-09-05T07:20:13Z_WhatEver.php         │ failed │ 2018-12-05 12:50:34 │ -                   │       0.021546 │
│ 2018-12-05T13:00:00Z_CreateUsersTable.php │   new  │ -                   │ -                   │                │
╰───────────────────────────────────────────┴────────┴─────────────────────┴─────────────────────┴────────────────╯'),
                    $this->mocks['console']->format($table) // remove formatting
                );
            });

        $command->handle($getOpt);
    }

    protected function buildStatus(Migration ...$migrations)
    {
        return (object)[
            'migrations' => $migrations,
            'count' => count(array_filter($migrations, function (Migration $migration) {
                return $migration->status !== 'done';
            }))
        ];
    }

    protected function buildGetopt(Command $command, string $args): GetOpt
    {
        $getOpt = new GetOpt();
        $getOpt->addCommand($command);
        $getOpt->process($args);

        return $getOpt;
    }
}

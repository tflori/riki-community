<?php

namespace Test\Unit\Cli\Command\Db;

use Breyta\Model\Migration;
use Breyta\Model\Statement;
use Hugga\Output\Drawing\ProgressBar;
use Test\TestCase;
use Mockery as m;

class BreytaCommandTest extends TestCase
{
    /** @test */
    public function startsAProgressBarWithTitleApplying()
    {
        $command = new Example($this->app, $this->mocks['console']);

        $this->mocks['console']->shouldReceive('addDrawing')->with(m::type(ProgressBar::class))
            ->once()->andReturnUsing(function (ProgressBar $progressBar) {
                self::assertSame('Applying', $this->getProtectedProperty($progressBar, 'title'));
                self::assertSame(23, $this->getProtectedProperty($progressBar, 'max'));
                self::assertSame('migrations', $this->getProtectedProperty($progressBar, 'type'));
                return true;
            });

        $command->start((object)[
            'task' => 'migrate',
            'count' => 23,
        ]);
    }

    /** @test */
    public function startsAProgressBarWithTitleReverting()
    {
        $command = new Example($this->app, $this->mocks['console']);

        $this->mocks['console']->shouldReceive('addDrawing')->with(m::type(ProgressBar::class))
            ->once()->andReturnUsing(function (ProgressBar $progressBar) {
                self::assertSame('Reverting', $this->getProtectedProperty($progressBar, 'title'));
                self::assertSame(3, $this->getProtectedProperty($progressBar, 'max'));
                self::assertSame('migrations', $this->getProtectedProperty($progressBar, 'type'));
                return true;
            });

        $command->start((object)[
            'task' => 'revert',
            'count' => 3,
        ]);
    }

    /** @test */
    public function writesOutThatThereAreNoMigrationsToExecute()
    {
        $command = new Example($this->app, $this->mocks['console']);

        $this->mocks['console']->shouldReceive('info')->with('No migrations to execute.')
            ->once();

        $task = (object) [
            'task' => 'migrate',
            'count' => 0,
        ];
        $command->start($task);
        $command->finish($task);
    }

    /** @test */
    public function showsAMessageWhenUpdateStarts()
    {
        $command = new Example($this->app, $this->mocks['console']);
        $command->start((object)['task' => 'migrate', 'count' => 3]);

        $this->mocks['console']->shouldReceive('line')->with(m::type('string'))
            ->once()->andReturnUsing(function ($text) {
                self::assertSame(
                    'Applying any/filename.php...',
                    $this->mocks['console']->format($text)
                );
            });

        $command->beforeMigration(Migration::createInstance([
            'file' => 'any/filename.php',
            'status' => 'new',
        ]));
    }

    /** @test */
    public function startsAProgressBarForEachStatementWithoutAction()
    {
        $command = new Example($this->app, $this->mocks['console']);
        $command->start((object)['task' => 'migrate', 'count' => 3]);

        $this->mocks['console']->shouldReceive('addDrawing')->with(m::type(ProgressBar::class))
            ->once()->andReturnUsing(function (ProgressBar $progressBar) {
                self::assertStringStartsWith(
                    'DOING SOMETHING ON DATABASE...',
                    $this->mocks['console']->format($progressBar->getText()) // remove formatting
                );
                self::assertSame(null, $this->getProtectedProperty($progressBar, 'max'));
                return true;
            });

        $command->beforeExecution(Statement::createInstance([
            'teaser' => 'DOING SOMETHING ON DATABASE...'
        ]));
    }

    /** @test */
    public function startsAProgressBarForEachStatementWithAction()
    {
        $command = new Example($this->app, $this->mocks['console']);
        $command->start((object)['task' => 'migrate', 'count' => 3]);

        $this->mocks['console']->shouldReceive('addDrawing')->with(m::type(ProgressBar::class))
            ->once()->andReturnUsing(function (ProgressBar $progressBar) {
                self::assertStringStartsWith(
                    'CREATE TABLE any_name',
                    $this->mocks['console']->format($progressBar->getText()) // remove formatting
                );
                self::assertSame(null, $this->getProtectedProperty($progressBar, 'max'));
                return true;
            });

        $command->beforeExecution(Statement::createInstance([
            'teaser' => 'CREATE TABLE any_name',
            'action' => 'create',
            'type' => 'table',
            'name' => 'any_name',
        ]));
    }

    /** @test */
    public function finishesTheProgressbarForExecution()
    {
        $statement = Statement::createInstance([
            'teaser' => 'CREATE TABLE any_name',
            'action' => 'create',
            'type' => 'table',
            'name' => 'any_name',
            'executionTime' => 0.23
        ]);
        $command = new Example($this->app, $this->mocks['console']);
        $command->start((object)['task' => 'migrate', 'count' => 3]);
        $command->beforeExecution($statement);

        $this->mocks['console']->shouldReceive('removeDrawing')->with(m::type(ProgressBar::class))
            ->once()->andReturnUsing(function (ProgressBar $progressBar) {
                self::assertSame(
                    'CREATE TABLE any_name ... done (0.23 seconds)',
                    $this->mocks['console']->format($progressBar->getText()) // remove formatting
                );
                return true;
            });

        $command->afterExecution($statement);
    }

    /** @test */
    public function finishesTheProgressbarForMigration()
    {
        $migration = Migration::createInstance([
            'file' => 'any/filename.php',
            'status' => 'done',
            'execution_time' => 2.3,
        ]);
        $command = new Example($this->app, $this->mocks['console']);
        $command->start((object)['task' => 'migrate', 'count' => 3]);
        $command->beforeMigration($migration);

        $this->mocks['console']->shouldReceive('line')->with(m::type('string'))
            ->once()->andReturnUsing(function ($text) {
                self::assertSame(
                    '... done (2.3 seconds)',
                    $this->mocks['console']->format($text) // remove formatting
                );
            });

        $command->afterMigration($migration);
    }

    /** @test */
    public function finishesTheProgressbar()
    {
        $task = (object)['task' => 'migrate', 'count' => 3];
        $command = new Example($this->app, $this->mocks['console']);
        $command->start($task);

        $this->mocks['console']->shouldReceive('removeDrawing')->with(m::type(ProgressBar::class))
            ->once()->andReturnUsing(function (ProgressBar $progressBar) {
                self::assertSame(
                    'Applying 3/3 migrations |███| 100%',
                    $this->mocks['console']->format($progressBar->getText()) // remove formatting
                );
                return true;
            });

        $command->finish($task);
    }

    /** @test */
    public function executesTheClosure()
    {
        $command = new Example($this->app, $this->mocks['console']);
        $calls = [];
        $spy = function () use (&$calls) {
            $calls[] = func_get_args();
        };

        $command->usingProgressBars($spy);

        self::assertCount(1, $calls);
        self::assertSame([], $calls[0]);
    }

    /** @test */
    public function removesRemainingProgressBars()
    {
        $command = new Example($this->app, $this->mocks['console']);

        $this->mocks['console']->shouldReceive('removeDrawing')->with(m::type(ProgressBar::class))
            ->once()->andReturnUsing(function (ProgressBar $progressBar) {
                self::assertSame(
                    'Applying 0/3 migrations |   |   0%',
                    $this->mocks['console']->format($progressBar->getText()) // remove formatting
                );
                return true;
            });

        $command->usingProgressBars(function () use ($command) {
            $command->start((object)['task' => 'migrate', 'count' => 3]);
        });
    }

    /** @test */
    public function removesRemainingProgressBarsWithExceptions()
    {
        $command = new Example($this->app, $this->mocks['console']);

        $this->mocks['console']->shouldReceive('removeDrawing')->with(m::type(ProgressBar::class))
            ->once()->andReturnUsing(function (ProgressBar $progressBar) {
                self::assertSame(
                    'Applying 0/3 migrations |   |   0%',
                    $this->mocks['console']->format($progressBar->getText()) // remove formatting
                );
                return true;
            });

        self::expectException(\RuntimeException::class);

        $command->usingProgressBars(function () use ($command) {
            $command->start((object)['task' => 'migrate', 'count' => 3]);
            throw new \RuntimeException('Any exception');
        });
    }
}

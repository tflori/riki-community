<?php

namespace App\Cli\Command\Db;

use App\Cli\Command\AbstractCommand;
use Breyta\Migrations;
use Breyta\Model\Migration;
use Breyta\Model\Statement;
use Breyta\ProgressInterface;
use Hugga\Output\Drawing\ProgressBar;

abstract class BreytaCommand extends AbstractCommand implements ProgressInterface
{
    /** @var ProgressBar */
    protected $progress;

    /** @var ProgressBar */
    protected $migrationProgress;

    /** @var ProgressBar */
    protected $statementProgress;

    /** @var string */
    protected $action = 'applying';

    /** @var Migrations */
    protected $breyta;

    protected $pid;

    public function start(\stdClass $info)
    {
        if ($info->count === 0) {
            $this->console->info('No migrations to execute.');
            return;
        }

        $this->action = $info->task === 'migrate' ? 'applying' : 'reverting';
        $title = ucfirst($this->action);
        $this->progress = new ProgressBar($this->console, $info->count, $title, 'migrations');
        $this->progress->start();
    }

    public function beforeMigration(Migration $migration)
    {
        $title = ucfirst($this->action) . ' ' . $migration->file;
        $this->migrationProgress = new ProgressBar($this->console, null, $title);
        $this->migrationProgress->start();
    }

    public function beforeExecution(Statement $statement)
    {
        if ($statement->action && $statement->name) {
            $title = sprintf(
                '${light-magenta}%s%s ${light-green}%s${r}',
                strtoupper($statement->action),
                ($statement->type ? ' ' . strtoupper($statement->type) : ''),
                $statement->name
            );
        } else {
            $title = sprintf('${light-yello}%s${r}', $statement->teaser);
        }

        $this->statementProgress = new ProgressBar($this->console, null, $title);
        $this->statementProgress->start();

        // skip forking of process in tests
        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_COMPOSER_INSTALL') && function_exists('pcntl_fork') && function_exists('posix_kill')) {
            // We fork the process to continue the progress bar drawing while the query is executed

            $pid = pcntl_fork();
            if ($pid) {
                // in the parent process we continue with the migration
                $this->pid = $pid;
                return;
            }

            // in the fork we start an endless loop
            while (true) {
                usleep(0.08 * 1000 * 1000);
                $this->statementProgress->advance();
                $this->migrationProgress->advance();
            }
            // to be sure we exit the fork (if true is not true...)
            exit;
        }
    }

    public function afterExecution(Statement $statement)
    {
        if ($this->pid) {
            // @codeCoverageIgnoreStart
            posix_kill($this->pid, SIGINT);
            $this->pid = null;
            // @codeCoverageIgnoreEnd
        }

        $this->statementProgress
            ->template(sprintf(
                '{title} ... ${green}done${r} (%.6g seconds)',
                $statement->executionTime
            ))
            ->finish();
    }

    public function afterMigration(Migration $migration)
    {
        $this->migrationProgress
            ->template(sprintf(
                '{title} ... ${green}done${r} (%.6g seconds)',
                $migration->executionTime
            ))
            ->finish();
        $this->progress->advance();
    }

    public function finish(\stdClass $info)
    {
        if ($this->progress) {
            $this->progress->finish();
        }
    }

    protected function getBreyta()
    {
        if (!$this->breyta) {
            $this->breyta = $this->app->make(
                Migrations::class,
                $this->app->entityManager->getConnection(),
                $this->app->environment->resourcePath('database', 'migrations')
            );
            $this->breyta->setProgress($this);
        }

        return $this->breyta;
    }

    protected function usingProgressBars(\Closure $closure)
    {
        try {
            return $closure();
        } finally {
            if ($this->pid) {
                // @codeCoverageIgnoreStart
                posix_kill($this->pid, SIGINT);
                $this->pid = null;
                // @codeCoverageIgnoreEnd
            }

            /** @var ProgressBar $progressBar */
            foreach ([$this->progress, $this->migrationProgress, $this->statementProgress] as $progressBar) {
                if ($progressBar) {
                    $this->console->removeDrawing($progressBar);
                }
            }
        }
    }
}

<?php

namespace App\Cli\Command\Db;

use App\Application;
use Breyta\Model\Migration;
use GetOpt\GetOpt;
use GetOpt\Option;
use Hugga\Console;
use Hugga\Output\Drawing\Table;

class Status extends BreytaCommand
{
    protected $name = 'db:status';

    protected $description = 'Show the status of the migrations.';

    public function __construct(Application $app, Console $console)
    {
        parent::__construct($app, $console);
        $this->addOption(
            Option::create(null, 'has-migrations')
                ->setDescription('Suppress output and just return true or false (use for scripting)')
        );
    }

    public function handle(GetOpt $getOpt): int
    {
        $status = $this->getBreyta()->getStatus();

        if ($getOpt->getOption('has-migrations')) {
            return $status->count > 0 ? 0 : 1;
        }

        if (!empty($status->migrations)) {
            $table = new Table($this->console, array_map(function (Migration $migration) {
                static $statusColors = [
                    'failed' => 'red',
                    'done' => 'green',
                ];

                return [
                    'name' => $migration->file,
                    'status' => sprintf(
                        '${b;%s}%s',
                        $statusColors[$migration->status] ?? 'yellow',
                        $migration->status
                    ),
                    'executed' => $migration->executed ? $migration->executed->format('Y-m-d H:i:s') : '-',
                    'reverted' => $migration->reverted ? $migration->reverted->format('Y-m-d H:i:s') : '-',
                    'executionTime' => sprintf('%.6g', $migration->execution_time),
                ];
            }, $status->migrations));
            $table->repeatHeaders(20)->setHeaders([
                'Name',
                'Status',
                'Executed',
                'Reverted',
                'Execution Time',
            ], true)->column('executionTime', [
                'align' => 'right'
            ])->column('status', [
                'align' => 'center',
            ])->draw();
        }

        $status->count > 0 ? $this->console->warn(sprintf(
            '%d migration%s need to be applied.',
            $status->count,
            $status->count === 1 ? '' : 's'
        )) : $this->console->info('No migrations need to be applied.');

        return 0;
    }
}

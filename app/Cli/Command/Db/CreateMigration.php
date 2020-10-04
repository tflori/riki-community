<?php

namespace App\Cli\Command\Db;

use App\Application;
use GetOpt\GetOpt;
use GetOpt\Operand;
use Hugga\Console;

class CreateMigration extends BreytaCommand
{
    protected $name = 'db:createMigration';

    protected $description = 'Create a new migration for <name>.';

    public function __construct(Application $app, Console $console)
    {
        parent::__construct($app, $console);
        $this->addOperand(Operand::create('name', Operand::REQUIRED));
    }

    /** @codeCoverageIgnore trivial command */
    public function handle(GetOpt $getOpt): int
    {
        $path = $this->getBreyta()->createMigration($getOpt->getOperand('name'));
        $path = preg_replace(
            '~^' . $this->app->environment->getBasePath() . '~',
            $this->app->config->env('PROJECT_PATH'),
            $path
        );

        $this->info('Created migration ' . $path);
        return 0;
    }
}

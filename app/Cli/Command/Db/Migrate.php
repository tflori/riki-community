<?php

namespace App\Cli\Command\Db;

use App\Application;
use GetOpt\GetOpt;
use GetOpt\Operand;
use Hugga\Console;

class Migrate extends BreytaCommand
{
    protected $name = 'db:migrate';

    protected $description = 'Apply all migrations or migrations before and including <to>.';

    public function __construct(Application $app, Console $console)
    {
        parent::__construct($app, $console);
        $this->addOperand(
            Operand::create('to', Operand::OPTIONAL)
                ->setDescription('Date in ISO format or file name of a migration')
        );
    }

    public function handle(GetOpt $getOpt): int
    {
        return $this->usingProgressBars(function () use ($getOpt) {
            $file = $getOpt->getOperand('to');
            if ($file) {
                return $this->getBreyta()->migrateTo($file) ? 0 : 1;
            }

            return $this->getBreyta()->migrate() ? 0 : 1;
        });
    }
}

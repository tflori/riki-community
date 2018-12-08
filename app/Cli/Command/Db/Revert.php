<?php

namespace App\Cli\Command\Db;

use App\Application;
use GetOpt\GetOpt;
use GetOpt\Operand;
use Hugga\Console;

class Revert extends BreytaCommand
{
    protected $name = 'db:revert';

    protected $description = 'Revert all migrations or migrations after and including <to>.';

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
                return $this->getBreyta()->revertTo($file) ? 0 : 1;
            }

            return $this->getBreyta()->revert() ? 0 : 1;
        });
    }
}

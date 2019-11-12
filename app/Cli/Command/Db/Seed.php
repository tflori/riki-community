<?php

namespace App\Cli\Command\Db;

use App\Application;
use App\Cli\Command\AbstractCommand;
use GetOpt\ArgumentException\Invalid;
use GetOpt\GetOpt;
use GetOpt\Operand;
use Hugga\Console;
use Seeder\AbstractSeeder;

class Seed extends AbstractCommand
{
    protected $name = 'db:seed';

    protected $description = 'Seed <seeder> or "Production" by default.';

    public function __construct(Application $app, Console $console)
    {
        parent::__construct($app, $console);
        $this->addOperand(Operand::create('seeder'));
    }


    public function handle(GetOpt $getOpt): int
    {
        $seederClass = 'Seeder\\' . ucfirst($getOpt->getOperand('seeder') ?? 'production') . 'Seeder';

        if (!class_exists($seederClass)) {
            throw new Invalid(sprintf('Seeder %s not found', $seederClass));
        }

        /** @var AbstractSeeder $seeder */
        $seeder = $this->app->make($seederClass, $this->app->entityManager);
        $seeder->sprout();

        return 0;
    }
}

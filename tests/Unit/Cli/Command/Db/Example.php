<?php

namespace Test\Unit\Cli\Command\Db;

use App\Cli\Command\Db\BreytaCommand;
use GetOpt\GetOpt;

class Example extends BreytaCommand
{
    protected $name = 'breyta:example';

    public function handle(GetOpt $getOpt): int
    {
    }

    public function usingProgressBars(\Closure $closure)
    {
        parent::usingProgressBars($closure);
    }
}

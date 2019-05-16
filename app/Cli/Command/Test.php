<?php

namespace App\Cli\Command;

use GetOpt\GetOpt;

class Test extends AbstractCommand
{
    protected $name = 'test';

    protected $description = 'Test your code on command line interface';

    /** @codeCoverageIgnore  */
    public function handle(GetOpt $getOpt): int
    {
        // write your code here but don't commit it (at least remove it before opening a PR)

        return 0;
    }
}

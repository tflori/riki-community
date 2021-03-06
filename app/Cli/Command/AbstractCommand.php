<?php

namespace App\Cli\Command;

use App\Application;
use App\Cli\Concerns\WritesToConsole;
use GetOpt\Command;
use GetOpt\GetOpt;
use Hugga\Console;

abstract class AbstractCommand extends Command
{
    use WritesToConsole;

    /** @var string */
    protected $description = '';

    /** @var Application */
    protected $app;

    /** @var Console */
    protected $console;

    public function __construct(Application $app, Console $console)
    {
        $this->app = $app;
        $this->console = $console;
        parent::__construct($this->name, [$this, 'handle']);
        if (!empty($this->description)) {
            $this->setDescription($this->description);
        }
    }

    abstract public function handle(GetOpt $getOpt): int;
}

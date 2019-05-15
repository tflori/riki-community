<?php

namespace App\Factory;

use Hugga\Console;

class ConsoleFactory extends AbstractFactory
{
    protected $shared = true;

    /**
     * @return Console
     * @codeCoverageIgnore ConsoleFactory get's mocked in tests
     */
    protected function build()
    {
        $console = new Console($this->container->get('logger'));
        $console->logMessages($this->container->config->env('LOG_CONSOLE', true));
        return $console;
    }
}

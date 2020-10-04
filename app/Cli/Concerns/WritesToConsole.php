<?php

namespace App\Cli\Concerns;

use Hugga\Console;

/** @codeCoverageIgnore wrapper for console methods */
trait WritesToConsole
{
    /** @var Console */
    protected $console;

    public function setConsole(Console $console)
    {
        $this->console = $console;
    }

    /**
     * Write $message to stdout
     *
     * @param string $message
     * @param int    $weight
     */
    protected function write(string $message, int $weight = Console::WEIGHT_NORMAL): void
    {
        $this->console->write($message, $weight);
    }

    /**
     * Write $message to stderr
     *
     * @param string $message
     * @param int    $weight
     */
    protected function writeError(string $message, int $weight = Console::WEIGHT_HIGH): void
    {
        $this->console->writeError($message, $weight);
    }

    /**
     * Shortcut to ->write('Your message' . PHP_EOL);
     *
     * @param string $message
     * @param int $weight
     */
    protected function line(string $message, int $weight = Console::WEIGHT_NORMAL): void
    {
        $this->console->line($message, $weight);
    }

    /**
     * Shortcut to ->write('${green;bold}Your message' . PHP_EOL)
     *
     * @param string $message
     * @param int $weight
     */
    protected function info(string $message, int $weight = Console::WEIGHT_NORMAL)
    {
        $this->console->info($message, $weight);
    }

    /**
     * Shortcut to ->write('${red;bold}Your message' . PHP_EOL, WEIGHT_HIGHER);
     *
     * @param string $message
     * @param int $weight
     */
    protected function warn(string $message, int $weight = Console::WEIGHT_HIGHER)
    {
        $this->console->warn($message, $weight);
    }

    /**
     * Write a highlighted error message (red bg; spacing) to stderr
     *
     * @param string $message
     * @param int    $weight
     */
    public function error(string $message, int $weight = Console::WEIGHT_HIGH): void
    {
        $this->console->error($message, $weight);
    }
}

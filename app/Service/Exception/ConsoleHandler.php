<?php

namespace App\Service\Exception;

use App\Application;
use App\Service\Exception\Formatter\Console as ConsoleFormatter;
use Hugga\Console;
use Whoops\Handler\Handler;

class ConsoleHandler extends Handler
{
    /**
     * @return int|null A handler may return nothing, or a Handler::HANDLE_* constant
     */
    public function handle()
    {
        $console = Application::console();

        /** @var ConsoleFormatter $formatter */
        $formatter = Application::app()->make(ConsoleFormatter::class, $this->getInspector());

        $console->logMessages(false);
        $console->writeError($formatter->formatMessage() . PHP_EOL);
        $console->writeError(
            PHP_EOL . '${b}Stack Trace:${r}' . PHP_EOL . $formatter->formatTrace() . PHP_EOL,
            Console::WEIGHT_NORMAL
        );

        return self::QUIT;
    }
}

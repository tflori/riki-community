<?php

namespace App\Service\Exception;

use App\Application;
use App\Service\Exception\Formatter\Log as LogFormatter;
use Whoops\Handler\Handler;

class LogHandler extends Handler
{
    public function handle()
    {
        $logger = Application::logger();

        /** @var LogFormatter $formatter */
        $formatter = Application::app()->make(LogFormatter::class, $this->getInspector());

        $logger->error($formatter->formatMessage());
        $logger->debug('Stack Trace: ' . $formatter->formatTrace());

        return Handler::DONE;
    }
}

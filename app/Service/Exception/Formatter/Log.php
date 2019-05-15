<?php

namespace App\Service\Exception\Formatter;

use App\Service\Exception\Formatter;
use Throwable;

class Log extends Formatter
{
    public function formatMessage(): string
    {
        return $this->generateMessage($this->inspector->getException());
    }

    public function formatTrace(): string
    {
        static $template = ' #%d %s->%s(%s) in %s on line %d';
        $frames = $this->inspector->getFrames();

        $response = '';

        $line = 1;
        foreach ($frames as $frame) {
            $response .= str_replace('{none}->', '', sprintf(
                $template,
                $line,
                $frame->getClass() ?: '{none}',
                $frame->getFunction(),
                $this->generateArgs($frame->getArgs()),
                $this->replacePath($frame->getFile()),
                $frame->getLine()
            ));

            $line++;
        }

        return substr($response, 1); // remove the first space
    }

    protected function generateMessage(Throwable $exception)
    {
        $message = sprintf(
            '%s%s: %s in %s on line %d',
            get_class($exception),
            $exception->getCode() ? '(' . $exception->getCode() . ')' : '',
            $exception->getMessage(),
            $this->replacePath($exception->getFile()),
            $exception->getLine()
        );

        if ($exception->getPrevious()) {
            $message .= ' [Caused by ' . $this->generateMessage($exception->getPrevious()) . ']';
        }

        return $message;
    }
}

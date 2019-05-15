<?php

namespace App\Service\Exception\Formatter;

use App\Service\Exception\Formatter;

class Console extends Formatter
{
    public function formatMessage(): string
    {
        return $this->generateMessage($this->inspector->getException());
    }

    public function formatTrace(): string
    {
        static $template = PHP_EOL . '${b}%3d.${r} ${light-magenta}%s->%s${r}(%s)' . PHP_EOL .
            '     ${grey}in ${blue}%s ${grey}on line ${yellow}%d${r}';
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

        return substr($response, strlen(PHP_EOL));
    }

    protected function generateMessage(\Throwable $exception): string
    {
        $message = sprintf(
            '${b}%s${yellow}%s${r}: ${red;b}%s ${grey}in ${blue}%s ${grey}on line ${yellow}%d${r}',
            get_class($exception),
            $exception->getCode() ? '(' . $exception->getCode() . ')' : '',
            $exception->getMessage(),
            $this->replacePath($exception->getFile()),
            $exception->getLine()
        );

        if ($exception->getPrevious()) {
            $message .= PHP_EOL . PHP_EOL . 'Caused by ' . $this->generateMessage($exception->getPrevious());
        }

        return $message;
    }
}

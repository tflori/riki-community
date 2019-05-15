<?php

namespace App\Service\Exception;

use App\Application as app;
use Whoops\Exception\Inspector;

abstract class Formatter
{
    /** @var Inspector */
    protected $inspector;

    /** @var array */
    protected $options = [];

    /**
     * Formatter constructor.
     *
     * @param Inspector $inspector
     * @param array     $options
     */
    public function __construct(Inspector $inspector, array $options = [])
    {
        $this->inspector = $inspector;
        $this->options   = array_merge_recursive($this->options, $options);
    }

    abstract public function formatMessage(): string;
    abstract public function formatTrace(): string;

    protected function replacePath(string $path): string
    {
        $projectPath = app::config()->env('PROJECT_PATH');
        if ($projectPath) {
            $path = preg_replace(
                '~^' . app::app()->getBasePath() . '~',
                $projectPath,
                $path
            );
        }

        return $path;
    }

    protected function generateArgs(array $args): string
    {
        $result = [];
        foreach ($args as $arg) {
            switch (gettype($arg)) {
                case 'object':
                    $result[] = get_class($arg);
                    break;

                case 'string':
                    if (!class_exists($arg)) {
                        $arg = strlen($arg) > 20 ? substr($arg, 0, 20) . '…' : $arg;
                    }
                    $result[] = sprintf('"%s"', $arg);
                    break;

                case 'integer':
                case 'double':
                    $result[] = (string)$arg;
                    break;

                case 'boolean':
                    $result[] = $arg ? 'true' : 'false';
                    break;

                default:
                    $result[] = gettype($arg);
                    break;
            }
        }
        return implode(', ', $result);
    }
}

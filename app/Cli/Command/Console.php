<?php

namespace App\Cli\Command;

use GetOpt\GetOpt;
use Psy\Shell;

/**
 * @codeCoverageIgnore
 */
class Console extends AbstractCommand
{
    protected $name = 'console';

    protected $description = 'Start an interactive console';

    public function handle(GetOpt $getOpt): int
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->app->environment->path('src'))
        );
        /** @var \SplFileInfo $f */
        foreach ($iterator as $f) {
            if (!$f->isFile() || !preg_match('/.php$/', $f->getFilename())) {
                continue;
            }
            require_once $f->getPathname();
        }

        /** @var Shell $shell */
        $shell = $this->app->make(Shell::class);
        $shell->setScopeVariables([
            'app' => $this->app,
            'em' => $this->app->entityManager,
            'config' => $this->app->config,
        ]);
        $shell->run();
        return 0;
    }
}

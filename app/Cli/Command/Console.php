<?php

namespace App\Cli\Command;

use GetOpt\GetOpt;
use ORM\Entity;
use Psy\Configuration;
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

        $config = new Configuration();
        $config->getPresenter()->addCasters([
            Entity::class => [self::class, 'castEntity'],
        ]);

        /** @var Shell $shell */
        $shell = $this->app->make(Shell::class, $config);
        $shell->setScopeVariables([
            'app' => $this->app,
            'em' => $this->app->entityManager,
            'config' => $this->app->config,
        ]);
        $shell->run();
        return 0;
    }

    public static function castEntity(Entity $entity)
    {
        return $entity->toArray();
    }
}

<?php

namespace App\Cli\Command\Config;

use App\Application;
use App\Cli\Command\AbstractCommand;
use App\Config;
use GetOpt\GetOpt;
use GetOpt\Option;
use Hugga\Console;

class Cache extends AbstractCommand
{
    protected $name = 'config:cache';

    protected $description = 'Create a configuration cache when caching is enabled for this environment.';

    public function __construct(Application $app, Console $console)
    {
        parent::__construct($app, $console);
        $this->addOption(Option::create(null, 'clear')
            ->setDescription('Clear the cache'));
        $this->addOption(Option::create('f', 'force')
            ->setDescription('Force cache if caching is disabled in this environment'));
    }


    public function handle(GetOpt $getOpt): int
    {
        if (!$this->app->environment->canCacheConfig() && !$getOpt->getOption('force')) {
            $this->warn('The environment does not allow to cache the configuration!');
            return 0;
        }

        $cachePath = $this->app->environment->getConfigCachePath();
        if (!file_exists(dirname($cachePath)) && !@mkdir(dirname($cachePath), umask() ^ 0777, true)) {
            $this->error('Could not create parent directory for caching!');
            return 1;
        } elseif (!is_writeable(dirname($cachePath)) || !is_dir(dirname($cachePath))) {
            $this->error('Cache directory is not writeable!');
            return 2;
        }

        if ($getOpt->getOption('clear')) {
            // remove the configuration cache
            if (file_exists($cachePath) && !@unlink($cachePath)) {
                // unreachable during tests
                // @codeCoverageIgnoreStart
                $this->error('Failed to clear the configuration cache!');
                return 4;
                // @codeCoverageIgnoreEnd
            }

            $this->info('Configuration cache cleared successfully!');
        } else {
            // create a fresh configuration (don't use the cached version)
            $config = new Config($this->app->environment);
            if (!@file_put_contents($cachePath, serialize($config))) {
                // unreachable during tests
                // @codeCoverageIgnoreStart
                $this->error('Failed to cache the configuration!');
                return 3;
                // @codeCoverageIgnoreEnd
            }

            $this->info('Configuration cache created successfully!');
        }
        return 0;
    }
}

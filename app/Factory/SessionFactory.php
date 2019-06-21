<?php

namespace App\Factory;

use NbSessions\SessionInstance;

/**
 * Class SessionFactory
 *
 * @package App\Factory
 * @author Thomas Flori <thflori@gmail.com>
 * @codeCoverageIgnore trivial code that can not be executed in tests
 */
class SessionFactory extends AbstractFactory
{
    protected $shared = true;

    protected function build()
    {
        $config = $this->container->config->sessionConfig;

        ini_set('session.save_handler', 'redis');
        session_save_path($config->getSavePath());

        return new SessionInstance($config->name, $config->cookie);
    }
}

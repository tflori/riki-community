<?php

namespace App\Factory;

use NbSessions\SessionInstance;

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

<?php

namespace App\Factory;

use ORM\EntityManager;

class EntityManagerFactory extends AbstractFactory
{
    protected $shared = true;

    protected function build()
    {
        $config = $this->container->config;
        return new EntityManager(array_merge($config->emConfig, [
            EntityManager::OPT_CONNECTION => function () {
                return $this->container->db;
            },
        ]));
    }
}

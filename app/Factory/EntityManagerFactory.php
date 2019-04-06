<?php

namespace App\Factory;

use ORM\EntityManager;

/** @codeCoverageIgnore  */
class EntityManagerFactory extends AbstractFactory
{
    protected $shared = true;

    /**
     * This method builds the instance.
     *
     * @return mixed
     */
    protected function build()
    {
        return new EntityManager([
            EntityManager::OPT_CONNECTION => $this->container->config->dbConfig,
            'tableNameTemplate' => '%short%s',
        ]);
    }
}

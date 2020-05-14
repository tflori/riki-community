<?php

namespace App\Factory;

use PDO;

class DbFactory extends AbstractFactory
{
    protected $shared = true;

    protected function build()
    {
        $dbConfig = $this->container->app->config->dbConfig;
        $pdo = new PDO(
            $dbConfig->getDsn(),
            $dbConfig->user,
            $dbConfig->pass,
            $dbConfig->attributes
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}

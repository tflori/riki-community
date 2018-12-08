<?php

namespace App;

use Monolog\Logger;
use ORM\DbConfig;
use Riki\Environment;

class Config extends \Riki\Config
{
    public $logLevel = Logger::WARNING;

    public $dbConfig;

    public function __construct(Environment $environment)
    {
        parent::__construct($environment);
        $this->logLevel = Logger::toMonologLevel($this->env('LOG_LEVEL', $this->logLevel));
        $this->dbConfig = new DbConfig(
            'pgsql',
            $this->env('DB_DATABASE', 'community'),
            $this->env('DB_USERNAME', 'community'),
            $this->env('DB_PASSWORD'),
            $this->env('DB_HOST', 'postgres'),
            $this->env('DB_PORT', '5432')
        );
    }
}

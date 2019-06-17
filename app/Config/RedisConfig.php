<?php

namespace App\Config;

class RedisConfig
{
    /** @var string */
    public $host;

    /** @var int */
    public $port = 6379;

    /** @var int */
    public $dbNumber = 0;

    /** @var string */
    public $prefix = '';

    /**
     * RedisConfig constructor.
     *
     * @param string $host
     * @param int    $port
     * @param int    $dbNumber
     * @param string $prefix
     */
    public function __construct(string $host, int $port = 6379, int $dbNumber = 0, string $prefix = '')
    {
        $this->host     = $host;
        $this->port     = $port;
        $this->dbNumber = $dbNumber;
        $this->prefix   = $prefix;
    }
}

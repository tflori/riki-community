<?php

namespace App\Config;

class SessionConfig extends RedisConfig
{
    /** @var string */
    public $name;

    /** @var array */
    public $cookie = [
        'lifetime'    => 604800, // one week in seconds
        'path'        => '/',
        'domain'      => null,
        'secure'      => true,
        'httponly'    => true,
    ];

    public function __construct(string $name, string $host, int $port = 6379, int $dbNumber = 0)
    {
        $prefix = $name . ':';
        $this->name = $name;
        parent::__construct($host, $port, $dbNumber, $prefix);
    }

    public function getSavePath(): string
    {
        $path = 'tcp://' . $this->host . ':' . $this->port;

        $params = [];
        if ($this->prefix) {
            $params['prefix'] = $this->prefix;
        }
        if ($this->dbNumber) {
            $params['database'] = $this->dbNumber;
        }

        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        return $path;
    }
}

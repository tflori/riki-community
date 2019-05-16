<?php

namespace App\Config;

class SmtpConfig
{
    /** @var string */
    public $host;

    /** @var int */
    public $port;

    /** @var string */
    public $username;

    /** @var string */
    public $password;

    /** @var string */
    public $secure;

    /**
     * SmtpConfig constructor.
     *
     * @param string $host
     * @param int    $port
     * @param string $username
     * @param string $password
     * @param string $secure
     */
    public function __construct(
        string $host = 'localhost',
        int $port = null,
        string $username = null,
        string $password = null,
        string $secure = ''
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->secure = $secure;
    }
}

<?php

namespace App;

use App\Config\SmtpConfig;
use Monolog\Logger;
use ORM\DbConfig;
use Riki\Environment;

class Config extends \Riki\Config
{
    public $logLevel = Logger::WARNING;

    /** @var DbConfig */
    public $dbConfig;

    /** @var SmtpConfig */
    public $smtpConfig;

    public $email = [
        'From' => ['riki@w00tserver.org' => 'ríki community'],
        'Subject' => 'Notification from ríki community',
        'X-Mailer' => 'riki application',
    ];

    public function __construct(Environment $environment)
    {
        parent::__construct($environment);
        $this->logLevel = Logger::toMonologLevel($this->env('LOG_LEVEL', $this->logLevel));

        $this->dbConfig = new DbConfig(
            'pgsql',
            $this->env('DB_DATABASE', 'riki_community'),
            $this->env('DB_USERNAME', 'riki'),
            $this->env('DB_PASSWORD'),
            $this->env('DB_HOST', 'postgres'),
            $this->env('DB_PORT', '5432')
        );

        $this->smtpConfig = new SmtpConfig(
            $this->env('SMTP_HOST', 'mailhog'),
            $this->env('SMTP_PORT', '1025'),
            $this->env('SMTP_USERNAME'),
            $this->env('SMTP_PASSWORD'),
            $this->env('SMTP_SECURITY', '')
        );
    }
}

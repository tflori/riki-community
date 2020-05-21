<?php

namespace App;

use App\Config\RedisConfig;
use App\Config\SessionConfig;
use App\Config\SmtpConfig;
use App\Service\IpHelper;
use Monolog\Logger;
use ORM\DbConfig;
use ORM\EntityManager;
use Riki\Config as BaseConfig;

/**
 * Application configuration
 *
 * Keep in mind that this file gets serialized for caching.
 *
 * @property Environment $environment
 */
class Config extends BaseConfig
{
    public $logLevel = Logger::WARNING;

    /** @var DbConfig */
    public $dbConfig;

    /** @var SmtpConfig */
    public $smtpConfig;

    /** @var SessionConfig */
    public $sessionConfig;

    /** @var RedisConfig */
    public $cacheConfig;

    /** @var string */
    public $fallbackUrl;

    public $email = [
        'headers' => [
            'From' => ['riki@w00tserver.org' => 'ríki community'],
            'Subject' => 'Notification from ríki community',
            'X-Mailer' => 'riki application',
        ],
        'texts' => [
            'salutation' => 'Hello %s!',
            'closing' => "Best regards\nYour ríki community",
            'domain' => 'riki.w00tserver.org',
        ],
    ];

    /** @var string The secret to verify recaptcha tokens */
    public $recaptchaSecret = '';

    public $frontEnd = [
        /** The site key to request a recaptcha token */
        'recaptchaKey' => '',
    ];

    /** A list of ip address ranges, ip addresses and regular expressions  where we trust forward headers
     *
     * null = we trust every forward header
     * [] = we never trust forward headers
     * @var null|array
     * @see IpHelper::isInRange() */
    public $trustedProxies = null;

    /** @var array
     * @see https://tflori.github.io/orm/configuration.html
     * @note please be reminded that the connection configuration is hardcoded overwritten by $dbConfig */
    public $emConfig = [
        EntityManager::OPT_TABLE_NAME_TEMPLATE => '%short%s'
    ];

    public function __construct(Environment $environment)
    {
        parent::__construct($environment);
        $this->logLevel = Logger::toMonologLevel($this->env('LOG_LEVEL', $this->logLevel));
        $this->fallbackUrl = $this->env('FALLBACK_URL', 'http://localhost');

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

        $this->sessionConfig = new SessionConfig(
            ['secure' => substr($this->fallbackUrl, 0, 5) === 'https'],
            $this->env('SESSION_NAME', 'riki_session'),
            $this->env('SESSION_HOST', 'redis'),
            $this->env('SESSION_PORT', 6379),
            $this->env('SESSION_DB', 0)
        );

        $this->cacheConfig = new RedisConfig(
            $this->env('CACHE_HOST', 'redis'),
            $this->env('CACHE_PORT', 6379),
            $this->env('CACHE_DB', 0),
            $this->env('CACHE_PREFIX', 'riki_community:')
        );

        $this->recaptchaSecret = $this->env('RECAPTCHA_SECRET', '');
        $this->frontEnd['recaptchaKey'] = $this->env('RECAPTCHA_KEY', '');

        $this->trustedProxies = $this->env('TRUSTED_PROXIES');
    }
}

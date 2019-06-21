<?php

namespace App\Factory;

use App\Service\Mailer;

/**
 * @codeCoverageIgnore
 */
class MailerFactory extends AbstractFactory
{
    protected $shared = true;

    /**
     * This method builds the instance.
     *
     * @return mixed
     */
    protected function build()
    {
        $smtpConfig = $this->container->config->smtpConfig;
        $options = [
            'host' => $smtpConfig->host,
            'port' => $smtpConfig->port,
            'username' => $smtpConfig->username,
            'password' => $smtpConfig->password,
            'secure' => $smtpConfig->secure,
        ];
        return new Mailer(array_filter($options));
    }
}

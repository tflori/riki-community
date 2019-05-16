<?php

namespace App\Service;

use App\Application as app;
use App\Exception\MailerException;
use App\Model\Mail;
use InvalidArgumentException;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use Nette\Mail\SmtpMailer;

class Mailer extends SmtpMailer
{
    /**
     * @param Message $mail
     *
     * @return void
     * @throws MailerException
     */
    public function send(Message $mail)
    {
        if (!$mail instanceof Mail) {
            throw new InvalidArgumentException('$mail has to be an instance of App\Model\Mail');
        }

        try {
            parent::send($mail);
            // @codeCoverageIgnoreStart
            // that would mean we sent an email and we cannot send an email during tests
            app::logger()->info('Sent ' . $mail);
            // @codeCoverageIgnoreEnd
        } catch (SendException $e) {
            throw new MailerException(sprintf('Failed to send %s', $mail), 0, $e);
        }
    }
}

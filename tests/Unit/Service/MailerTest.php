<?php

namespace Test\Unit\Service;

use App\Exception\MailerException;
use App\Model\Mail;
use App\Service\Mailer;
use InvalidArgumentException;
use Nette\Mail\Message;
use Test\TestCase;

class MailerTest extends TestCase
{
    /** @test */
    public function throwsWhenNotUsingMailClass()
    {
        $mailer = new Mailer();
        $message = new Message();

        self::expectException(InvalidArgumentException::class);

        $mailer->send($message);
    }

    /** @test */
    public function throwsWhenSmtpServerIsOffline()
    {
        $mailer = new Mailer(['host' => 'localhost', 'port' => 55666]); // any port
        $mail = new Mail();
        $mail->setSubject('Test Message');
        $mail->addTo('john@example.com');

        self::expectException(MailerException::class);
        self::expectExceptionMessage('Failed to send ' . $mail);

        $mailer->send($mail);
    }
}

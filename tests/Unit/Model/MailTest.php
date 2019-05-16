<?php

namespace Test\Unit\Model;

use App\Model\Mail;
use Test\TestCase;

class MailTest extends TestCase
{
    /** @test */
    public function isConvertibleToString()
    {
        $mail = new Mail();

        self::assertSame('eMail', (string)$mail);
    }

    /** @test */
    public function addsTheRecipientToStringRepresentation()
    {
        $mail = new Mail();
        $mail->addTo('john@example.com', 'John Doe');

        self::assertSame('eMail to john@example.com', (string)$mail);
    }

    /** @test */
    public function addsAllRecipientsToStringRepresentation()
    {
        $mail = new Mail();
        $mail->addTo('john@example.com');
        $mail->addTo('jane@example.com');

        self::assertSame('eMail to john@example.com, jane@example.com', (string)$mail);
    }

    /** @test */
    public function addsTheSenderToStringRepresentation()
    {
        $mail = new Mail();
        $mail->setFrom('john@example.com');
        $mail->addTo('jane@example.com');

        self::assertSame('eMail from john@example.com to jane@example.com', (string)$mail);
    }

    /** @test */
    public function addsTheSubjectToStringRepresentation()
    {
        $mail = new Mail();
        $mail->addTo('john@example.com');
        $mail->setSubject('Your order has been shipped');

        self::assertSame('eMail to john@example.com with subject "Your order has been shipped"', (string)$mail);
    }
}

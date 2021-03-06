<?php

namespace Test\Unit\Factory;

use App\Factory\MailFactory;
use App\Model\Mail;
use Test\TestCase;

class MailFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->views->getLocator('mail')->add('test', $this->resourcePath('mail', 'body.php'));
        $this->app->views->getLocator('layout')->add('mail', $this->resourcePath('mail', 'layout.php'));
    }

    /** @test */
    public function rendersTheBody()
    {
        $factory = new MailFactory($this->app);

        /** @var Mail $mail */
        $mail = $factory->getInstance('test', [
            'name' => 'John Doe',
        ]);

        self::assertSame(
            <<<'BODY'
Hello John Doe,

**Note:** This is an automated message.
BODY
            ,
            $mail->getBody()
        );
    }

    /** @test */
    public function rendersTheMailLayout()
    {
        $factory = new MailFactory($this->app);

        /** @var Mail $mail */
        $mail = $factory->getInstance('test', [
            'name' => 'John Doe',
        ]);

        self::assertStringContainsString('<title>This is a test</title>', $mail->getHtmlBody());
        self::assertStringContainsString(
            <<<'HTMLBODY'
<p>Hello John Doe,</p>
<p><strong>Note:</strong> This is an automated message.</p>
HTMLBODY
            ,
            $mail->getHtmlBody()
        );
    }

    /** @test */
    public function setsTheBasicHeaders()
    {
        $factory = new MailFactory($this->app);

        /** @var Mail $mail */
        $mail = $factory->getInstance('test', [
            'name' => 'John Doe',
        ]);

        self::assertSame($this->app->config->email['headers']['From'], $mail->getFrom());
        self::assertSame($this->app->config->email['headers']['X-Mailer'], $mail->getHeader('X-Mailer'));
    }

    /** @test */
    public function setsTheSubject()
    {
        $factory = new MailFactory($this->app);

        /** @var Mail $mail */
        $mail = $factory->getInstance('test', [
            'name' => 'John Doe',
        ]);

        self::assertSame('This is a test', $mail->getSubject());
    }
}

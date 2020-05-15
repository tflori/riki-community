<?php

namespace Test\Unit\Http\Controller\UserController;

use App\Factory\MailFactory;
use App\Http\Controller\UserController;
use App\Model\Mail;
use App\Model\Request;
use Community\Model\Token\ActivationCode;
use Community\Model\Token\ActivationToken;
use Community\Model\User;
use function GuzzleHttp\Psr7\stream_for;
use Mockery as m;
use Test\TestCase;

class ResendActivationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->ormAllowInsert(ActivationCode::class, [
            'id' => rand(1000000, 2000000),
        ])->byDefault();
        $this->ormAllowInsert(ActivationToken::class, [
            'id' => rand(1000000, 2000000),
        ])->byDefault();
    }

    /** @test */
    public function requiresAuthentication()
    {
        $request = (new Request('POST', '/user/activate'));
        $controller = new UserController($this->app, $request);

        $response = $controller->resendActivation($request);

        self::assertSame(401, $response->getStatusCode());
    }

    /** @test */
    public function requiresStatusPending()
    {
        $this->signIn(['accountStatus' => User::DISABLED]);
        $request = (new Request('GET', '/user/resendActivation', ['Accept' => 'application/json']));
        $controller = new UserController($this->app, $request);

        $response = $controller->resendActivation($request);

        self::assertSame(400, $response->getStatusCode());
        self::assertArraySubset([
            'message' => 'Account disabled',
        ], json_decode($response->getBody(), true));
    }

    /** @test */
    public function createsAnActivationCode()
    {
        $this->signIn(['accountStatus' => User::PENDING]);
        $request = (new Request('GET', '/user/resendActivation', ['Accept' => 'application/json']))
            ->withAttribute('csrfTokenVerified', true);
        $controller = new UserController($this->app, $request);

        $this->ormExpectInsert(ActivationCode::class, [
            'id' => rand(1, 1000),
        ]);

        $controller->resendActivation($request);
    }

    /** @test */
    public function createsAnActivationToken()
    {
        $this->signIn(['accountStatus' => User::PENDING]);
        $request = (new Request('GET', '/user/resendActivation', ['Accept' => 'application/json']))
            ->withAttribute('csrfTokenVerified', true);
        $controller = new UserController($this->app, $request);

        $this->ormExpectInsert(ActivationToken::class, [
            'id' => rand(1, 1000),
        ]);

        $controller->resendActivation($request);
    }

    /** @test */
    public function buildsARegistrationMail()
    {
        $factory = m::mock(MailFactory::class);
        $this->app->add('mail', $factory);
        $this->signIn(['accountStatus' => User::PENDING]);
        $request = (new Request('GET', '/user/resendActivation', ['Accept' => 'application/json']))
            ->withAttribute('csrfTokenVerified', true);
        $controller = new UserController($this->app, $request);

        $factory->shouldReceive('getInstance')->with('user/resendActivation', m::type('array'))
            ->once()->andReturnUsing(function ($name, $data) {
                self::assertInstanceOf(User::class, $data['user']);
                self::assertNotEmpty($data['activationLink']);
                self::assertNotEmpty($data['activationCode']);
                return new Mail();
            });

        $controller->resendActivation($request);
    }

    /** @test */
    public function sendsAnEmailForActivation()
    {
        $mail = m::mock(Mail::class);
        $this->app->instance('mail', $mail);
        $user = $this->signIn(['accountStatus' => User::PENDING]);
        $request = (new Request('GET', '/user/resendActivation', ['Accept' => 'application/json']))
            ->withAttribute('csrfTokenVerified', true);
        $controller = new UserController($this->app, $request);

        $mail->shouldReceive('addTo')->with($user->email)
            ->once()->andReturnSelf();
        $this->mocks['mailer']->shouldReceive('send')->with($mail)
            ->once();

        $controller->resendActivation($request);
    }
}

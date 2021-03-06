<?php

namespace Test\Unit\Http\Controller\UserController;

use App\Factory\MailFactory;
use App\Http\Controller\UserController;
use App\Model\Mail;
use App\Model\Request;
use Carbon\Carbon;
use Community\Model\Token\ActivationCode;
use Community\Model\Token\ActivationToken;
use Community\Model\User;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;
use InvalidArgumentException;
use Mockery as m;
use Test\TestCase;

class RegisterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::$bcryptCost = 4;
        $this->ormAllowInsert(User::class, [
            'id' => rand(1000000, 2000000),
            'created' => Carbon::now('UTC')->format('Y-m-d H:i:s.u'),
            'updated' => Carbon::now('UTC')->format('Y-m-d H:i:s.u'),
            'account_status' => 'pending',
        ])->byDefault();
        $this->ormAllowInsert(ActivationCode::class, [
            'id' => rand(1000000, 2000000),
        ])->byDefault();
        $this->ormAllowInsert(ActivationToken::class, [
            'id' => rand(1000000, 2000000),
        ])->byDefault();

        $this->mocks['httpClient']->shouldReceive('post')
            ->with('https://www.google.com/recaptcha/api/siteverify', m::andAnyOtherArgs())
            ->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'success' => true,
                'score' => 1.0
            ])))->byDefault();
    }

    /** @test */
    public function verifiesRecaptchaToken()
    {
        $this->app->config->recaptchaSecret = 'r4nd0m';
        $request = (new Request('POST', '/register', ['Content-Type' => 'application/json']))
            ->withBody(stream_for(json_encode(array_merge(
                $this->getValidUserData(),
                [ 'recaptchaToken' => 'abc123' ]
            ))));
        $this->mocks['httpClient']->shouldReceive('post')
            ->with('https://www.google.com/recaptcha/api/siteverify', m::on(function ($options) use ($request) {
                self::assertArraySubset([
                    'form_params' => [
                        'secret' => 'r4nd0m',
                        'response' => 'abc123',
                        'remoteip' => $request->getIp(),
                    ],
                ], $options);
                return true;
            }))
            ->once()->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'success' => true,
                'score' => 1.0,
            ])));

        $controller = new UserController($this->app, $request);
        $controller->register($request);
    }

    /** @test */
    public function doesNotAcceptUnsuccessfulVerification()
    {
        $this->app->config->recaptchaSecret = 'r4nd0m';
        $request = (new Request('POST', '/register', ['Content-Type' => 'application/json']))
            ->withBody(stream_for(json_encode($this->getValidUserData())));
        $this->mocks['httpClient']->shouldReceive('post')
            ->with('https://www.google.com/recaptcha/api/siteverify', m::andAnyOtherArgs())
            ->once()->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'success' => false,
                'score' => 1.0,
            ])));

        $controller = new UserController($this->app, $request);
        $response = $controller->register($request);

        self::assertGreaterThanOrEqual(400, $response->getStatusCode());
    }

    /** @test */
    public function doesNotAcceptIncompleteVerificationResponse()
    {
        $this->app->config->recaptchaSecret = 'r4nd0m';
        $request = (new Request('POST', '/register', ['Content-Type' => 'application/json']))
            ->withBody(stream_for(json_encode($this->getValidUserData())));
        $this->mocks['httpClient']->shouldReceive('post')
            ->with('https://www.google.com/recaptcha/api/siteverify', m::andAnyOtherArgs())
            ->once()->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'result' => 'valid',
            ])));

        $controller = new UserController($this->app, $request);
        $response = $controller->register($request);

        self::assertGreaterThanOrEqual(400, $response->getStatusCode());
    }

    /** @test */
    public function doesNotAcceptFailedVerification()
    {
        $this->app->config->recaptchaSecret = 'r4nd0m';
        $request = (new Request('POST', '/register', ['Content-Type' => 'application/json']))
            ->withBody(stream_for(json_encode($this->getValidUserData())));
        $this->mocks['httpClient']->shouldReceive('post')
            ->with('https://www.google.com/recaptcha/api/siteverify', m::andAnyOtherArgs())
            ->once()->andReturn(new Response(500));

        $controller = new UserController($this->app, $request);
        $response = $controller->register($request);

        self::assertGreaterThanOrEqual(400, $response->getStatusCode());
    }

    /** @test */
    public function doesNotAcceptInvalidVerification()
    {
        $this->app->config->recaptchaSecret = 'r4nd0m';
        $request = (new Request('POST', '/register', ['Content-Type' => 'application/json']))
            ->withBody(stream_for(json_encode($this->getValidUserData())));
        $this->mocks['httpClient']->shouldReceive('post')
            ->with('https://www.google.com/recaptcha/api/siteverify', m::andAnyOtherArgs())
            ->once()->andReturn(new Response(200, ['Content-Type' => 'text/html'], '<h1>OK</h1>'));

        $controller = new UserController($this->app, $request);
        $response = $controller->register($request);

        self::assertGreaterThanOrEqual(400, $response->getStatusCode());
    }

    /** @test */
    public function doesNotAcceptScoresUnderZeroDotFive()
    {
        $this->app->config->recaptchaSecret = 'r4nd0m';
        $request = (new Request('POST', '/register', ['Content-Type' => 'application/json']))
            ->withBody(stream_for(json_encode($this->getValidUserData())));
        $this->mocks['httpClient']->shouldReceive('post')
            ->with('https://www.google.com/recaptcha/api/siteverify', m::andAnyOtherArgs())
            ->once()->andReturn(new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'success' => true,
                'score' => 0.49,
            ])));

        $controller = new UserController($this->app, $request);
        $response = $controller->register($request);

        self::assertGreaterThanOrEqual(400, $response->getStatusCode());
    }

    /** @test */
    public function expectsJsonEncodedBody()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid json provided in body');
        $request = (new Request('POST', '/register', ['Content-Type' => 'application/json']))
            ->withBody(stream_for('name=john&displayName=john'));

        $controller = new UserController($this->app, $request);
        $controller->register($request);
    }

    /** @dataProvider provideRequiredFields
     * @param string $field
     * @test */
    public function requiresField($field)
    {
        $request = (new Request('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]))->withBody(stream_for(json_encode($this->getIncompleteUserData($field))));

        $controller = new UserController($this->app, $request);
        $response = $controller->register($request);

        self::assertJson($response->getBody());
        self::assertArraySubset(
            ['errors' => [$field => ['Value should not be empty']]],
            json_decode($response->getBody(), true)
        );
    }

    /** @dataProvider provideValidatedFields
     * @param string $field
     * @test */
    public function validates($field)
    {
        $validationError = $this->getExampleUserData()[$field]['validationError'];
        $request = (new Request('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]))->withBody(stream_for(json_encode($this->getInvalidUserData($field))));

        $controller = new UserController($this->app, $request);
        $response = $controller->register($request);

        self::assertJson($response->getBody());
        self::assertArraySubset(
            ['errors' => [$field => [$validationError]]],
            json_decode($response->getBody(), true)
        );
    }

    /** @test */
    public function requiresPasswordConfirmation()
    {
        $userData = $this->getValidUserData();
        $userData['passwordConfirmation'] = 'different';
        $request = (new Request('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]))->withBody(stream_for(json_encode($userData)));

        $controller = new UserController($this->app, $request);
        $response = $controller->register($request);

        self::assertJson($response->getBody());
        self::assertArraySubset(
            ['errors' => ['password' => ['Passwords don\'t match']]],
            json_decode($response->getBody(), true)
        );
    }

    /** @test */
    public function expectsUniqueEmail()
    {
        $data = $this->getValidUserData();
        $this->ormAddResult(User::class, new User())
            ->where('email', $data['email']);

        $request = (new Request('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]))->withBody(stream_for(json_encode($data)));

        $controller = new UserController($this->app, $request);
        $response = $controller->register($request);

        self::assertJson($response->getBody());
        self::assertArraySubset(
            ['errors' => ['email' => ['Email address already taken']]],
            json_decode($response->getBody(), true)
        );
    }

    /** @test */
    public function expectsUniqueDisplayName()
    {
        $data = $this->getValidUserData();
        $this->ormAddResult(User::class, new User())
            ->where('displayName', $data['displayName']);
        $request = (new Request('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]))->withBody(stream_for(json_encode($data)));

        $controller = new UserController($this->app, $request);
        $response = $controller->register($request);

        self::assertJson($response->getBody());
        self::assertArraySubset(
            ['errors' => ['displayName' => ['Display name already taken']]],
            json_decode($response->getBody(), true)
        );
    }

    /** @test */
    public function createsAUser()
    {
        $this->ormExpectInsert(User::class, [
            'id' => rand(1, 1000),
            'created' => Carbon::now('UTC')->format('Y-m-d H:i:s.u'),
            'updated' => Carbon::now('UTC')->format('Y-m-d H:i:s.u'),
            'account_status' => 'pending',
        ]);
        $request = (new Request('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]))->withBody(stream_for(json_encode($this->getValidUserData())));

        $controller = new UserController($this->app, $request);
        $controller->register($request);
    }

    /** @test */
    public function createsAnActivationCode()
    {
        $this->ormExpectInsert(ActivationCode::class, [
            'id' => rand(1, 1000),
        ]);
        $request = (new Request('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]))->withBody(stream_for(json_encode($this->getValidUserData())));

        $controller = new UserController($this->app, $request);
        $controller->register($request);
    }

    /** @test */
    public function createsAnActivationToken()
    {
        $this->ormExpectInsert(ActivationToken::class, [
            'id' => rand(1, 1000),
        ]);
        $request = (new Request('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]))->withBody(stream_for(json_encode($this->getValidUserData())));

        $controller = new UserController($this->app, $request);
        $controller->register($request);
    }

    /** @test */
    public function buildsARegistrationMail()
    {
        $factory = m::mock(MailFactory::class);
        $this->app->add('mail', $factory);
        $request = (new Request('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]))->withBody(stream_for(json_encode($this->getValidUserData())));

        $factory->shouldReceive('getInstance')->with('user/registration', m::type('array'))
            ->once()->andReturnUsing(function ($name, $data) {
                self::assertInstanceOf(User::class, $data['user']);
                self::assertNotEmpty($data['activationLink']);
                self::assertNotEmpty($data['activationCode']);
                return new Mail();
            });

        $controller = new UserController($this->app, $request);
        $controller->register($request);
    }

    /** @test */
    public function sendsAnEmailForActivation()
    {
        $mail = m::mock(Mail::class);
        $this->app->instance('mail', $mail);
        $userData = $this->getValidUserData();
        $request = (new Request('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]))->withBody(stream_for(json_encode($userData)));

        $mail->shouldReceive('addTo')->with($userData['email'])
            ->once()->andReturnSelf();
        $this->mocks['mailer']->shouldReceive('send')->with($mail)
            ->once();

        $controller = new UserController($this->app, $request);
        $controller->register($request);
    }

    /** @test */
    public function returnsTheCreatedUser()
    {
        $userData = $this->getValidUserData();
        $request = (new Request('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]))->withBody(stream_for(json_encode($userData)));

        $this->ormExpectInsert(User::class, [
            'id' => $id = rand(1, 1000),
            'created' => ($created = Carbon::now('UTC'))->format('Y-m-d H:i:s.u'),
            'updated' => ($updated = Carbon::now('UTC'))->format('Y-m-d H:i:s.u'),
            'account_status' => 'pending',
        ]);

        $controller = new UserController($this->app, $request);
        $response = $controller->register($request);

        self::assertJson($response->getBody());
        self::assertArraySubset([
            'id' => $id,
            'name' => $userData['name'],
            'displayName' => $userData['displayName'],
            'email' => $userData['email'],
            'accountStatus' => 'pending',
            'created' => $created->format('Y-m-d\TH:i:s.u\Z'),
            'updated' => $updated->format('Y-m-d\TH:i:s.u\Z'),
        ], json_decode($response->getBody(), true));
    }


    public function provideRequiredFields()
    {
        return [
            ['email'],
            ['password'],
            ['displayName'],
        ];
    }

    public function provideValidatedFields()
    {
        return [
            ['email'],
            ['password'],
            ['displayName'],
            ['name'],
        ];
    }

    protected function getIncompleteUserData($missingField)
    {
        $data = array_map(function ($field) {
            return $field['valid'];
        }, $this->getExampleUserData());
        unset($data[$missingField]);
        return $data;
    }

    protected function getInvalidUserData($invalidField)
    {
        $exampleUserData = $this->getExampleUserData();
        $data = array_map(function ($field) {
            return $field['valid'];
        }, $exampleUserData);
        $data[$invalidField] = $exampleUserData[$invalidField]['invalid'];
        return $data;
    }

    public function getValidUserData()
    {
        return array_map(function ($field) {
            return $field['valid'];
        }, $this->getExampleUserData());
    }

    protected function getExampleUserData()
    {
        return [
            'email' => [
                'valid' => 'john.doe@example.com',
                'invalid' => 'john.doe',
                'validationError' => 'Value should be a valid email address'
            ],
            'password' => [
                'valid' => 'S4cr3d F4rt',
                'invalid' => 'too simple',
                'validationError' => 'Password strength 35; 50 needed'
            ],
            'passwordConfirmation' => [
                'valid' => 'S4cr3d F4rt',
            ],
            'displayName' => [
                'valid' => 'J. D.',
                'invalid' => 'char < or "',
                'validationError' => 'Only word characters, spaces, dots, dashes and at signs are allowed'
            ],
            'name' => [
                'valid' => 'Çıplak-Koyun',
                'invalid' => 'two_of_two',
                'validationError' => 'Only letters, numbers, spaces, dots and dashes are allowed'
            ],
        ];
    }
}

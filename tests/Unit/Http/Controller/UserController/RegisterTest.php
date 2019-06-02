<?php

namespace Test\Unit\Http\Controller\UserController;

use App\Http\Controller\UserController;
use Carbon\Carbon;
use Community\Model\Token\ActivationCode;
use Community\Model\Token\ActivationToken;
use Community\Model\User;
use function GuzzleHttp\Psr7\stream_for;
use InvalidArgumentException;
use Tal\ServerRequest;
use Test\TestCase;

class RegisterTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->ormAllowInsert(User::class, [
            'id' => $id = rand(1000000, 2000000),
            'created' => ($created = Carbon::now('UTC'))->format('Y-m-d H:i:s.u'),
            'updated' => ($updated = Carbon::now('UTC'))->format('Y-m-d H:i:s.u'),
            'account_status' => 'pending',
        ])->byDefault();
        $this->ormAllowInsert(ActivationCode::class, [
            'id' => rand(1000000, 2000000),
        ])->byDefault();
        $this->ormAllowInsert(ActivationToken::class, [
            'id' => rand(1000000, 2000000),
        ])->byDefault();
    }

    /** @test */
    public function expectsJsonEncodedBody()
    {
        $controller = new UserController('register');
        $request = (new ServerRequest('POST', '/register', ['Content-Type' => 'application/json']))
            ->withBody(stream_for('name=john&displayName=john'));

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid json provided in body');

        $controller->handle($request);
    }

    /** @dataProvider provideRequiredFields
     * @param string $field
     * @test */
    public function requires($field)
    {
        $controller = new UserController('register');
        $request = (new ServerRequest('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]))->withBody(stream_for(json_encode($this->getIncompleteUserData($field))));

        $response = $controller->handle($request);

        self::assertJson($response->getBody());
        self::assertArraySubset(
            ['errors' => [$field => [['key' => 'IS_EMPTY']]]],
            json_decode($response->getBody(), true)
        );
    }

    /** @dataProvider provideValidatedFields
     * @param string $field
     * @test */
    public function validates($field)
    {
        $validationError = $this->getExampleUserData()[$field]['validationError'];
        $controller = new UserController('register');
        $request = (new ServerRequest('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]))->withBody(stream_for(json_encode($this->getInvalidUserData($field))));

        $response = $controller->handle($request);

        self::assertJson($response->getBody());
        self::assertArraySubset(
            ['errors' => [$field => [['key' => $validationError]]]],
            json_decode($response->getBody(), true)
        );
    }

    /** @test */
    public function expectsUniqueEmail()
    {
        $data = $this->getValidUserData();
        $this->addFetcherResult(User::class, [
            sprintf('/"t0"\."email" = %s/', $this->mocks['pdo']->quote($data['email'])),
        ], new User());
        $controller = new UserController('register');
        $request = (new ServerRequest('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]))->withBody(stream_for(json_encode($data)));

        $response = $controller->handle($request);

        self::assertJson($response->getBody());
        self::assertArraySubset(
            ['errors' => ['email' => [['key' => 'EMAIL_TAKEN']]]],
            json_decode($response->getBody(), true)
        );
    }

    /** @test */
    public function expectsUniqueDisplayName()
    {
        $data = $this->getValidUserData();
        $this->addFetcherResult(User::class, [
            sprintf('/"t0"\."display_name" = %s/', $this->mocks['pdo']->quote($data['displayName'])),
        ], new User());
        $controller = new UserController('register');
        $request = (new ServerRequest('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]))->withBody(stream_for(json_encode($data)));

        $response = $controller->handle($request);

        self::assertJson($response->getBody());
        self::assertArraySubset(
            ['errors' => ['displayName' => [['key' => 'DISPLAY_NAME_TAKEN']]]],
            json_decode($response->getBody(), true)
        );
    }

    /** @test */
    public function createsAUser()
    {
        $controller = new UserController('register');
        $request = (new ServerRequest('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]))->withBody(stream_for(json_encode($this->getValidUserData())));

        $this->ormExpectInsert(User::class, [
            'id' => rand(1, 1000),
            'created' => Carbon::now('UTC')->format('Y-m-d H:i:s.u'),
            'updated' => Carbon::now('UTC')->format('Y-m-d H:i:s.u'),
            'account_status' => 'pending',
        ]);

        $controller->handle($request);
    }

    /** @test */
    public function createAnActivationCode()
    {
        $controller = new UserController('register');
        $request = (new ServerRequest('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]))->withBody(stream_for(json_encode($this->getValidUserData())));

        $this->ormExpectInsert(ActivationCode::class, [
            'id' => rand(1, 1000),
        ]);

        $controller->handle($request);
    }

    /** @test */
    public function createAnActivationToken()
    {
        $controller = new UserController('register');
        $request = (new ServerRequest('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]))->withBody(stream_for(json_encode($this->getValidUserData())));

        $this->ormExpectInsert(ActivationToken::class, [
            'id' => rand(1, 1000),
        ]);

        $controller->handle($request);
    }

    /** @test */
    public function returnsTheCreatedUser()
    {
        $controller = new UserController('register');
        $userData = $this->getValidUserData();
        $request = (new ServerRequest('POST', '/register', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]))->withBody(stream_for(json_encode($userData)));

        $this->ormExpectInsert(User::class, [
            'id' => $id = rand(1, 1000),
            'created' => ($created = Carbon::now('UTC'))->format('Y-m-d H:i:s.u'),
            'updated' => ($updated = Carbon::now('UTC'))->format('Y-m-d H:i:s.u'),
            'account_status' => 'pending',
        ]);

        $response = $controller->handle($request);

        self::assertJson($response->getBody());
        self::assertSame([
            'id' => $id,
            'name' => $userData['name'],
            'displayName' => $userData['displayName'],
            'email' => $userData['email'],
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
                'validationError' => 'NO_EMAIL_ADDRESS'
            ],
            'password' => [
                'valid' => 'S4cr3d F4rt',
                'invalid' => 'too simple',
                'validationError' => 'PASSWORD_TO_WEAK'
            ],
            'passwordConfirmation' => [
                'valid' => 'S4cr3d F4rt',
                'invalid' => 'differs',
                'validationError' => 'NOT_EQUAL'
            ],
            'displayName' => [
                'valid' => 'J. D.',
                'invalid' => 'char < or "',
                'validationError' => 'NO_MATCH'
            ],
            'name' => [
                'valid' => 'Çıplak-Koyun',
                'invalid' => 'two_of_two',
                'validationError' => 'NO_MATCH'
            ],
        ];
    }

}

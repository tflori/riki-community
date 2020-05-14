<?php /** @noinspection PhpDocMissingThrowsInspection */

namespace Test;

use App\Application;
use App\Config;
use App\Environment;
use App\Service\Cache;
use App\Service\Mailer;
use Carbon\Carbon;
use Community\Model\User;
use GuzzleHttp\Client;
use Hugga\Console;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nette\Mail\Message;
use ORM\EntityManager;
use ORM\MockTrait;
use ORM\Testing\EntityFetcherMock\ResultRepository;
use ReflectionClass;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Test\Extension\ArraySubsetAssert;
use Whoops;

abstract class TestCase extends MockeryTestCase
{
    use MockTrait, ArraySubsetAssert;

    /** @var Application|m\Mock */
    protected $app;

    /** @var EntityManager|m\Mock */
    protected $em;

    /** @var m\Mock[] */
    protected $mocks = [];

    /** @var string */
    protected $basePath;

    protected function setUp()
    {
        parent::setUp();
        $this->initApplication(realpath(__DIR__ . '/..'));
    }

    protected function tearDown()
    {
        parent::tearDown();
        Application::app()->destroy();
    }


    public function initApplication($basePath)
    {
        $this->basePath = $basePath;

        $whoops = new Whoops\Run(m::mock(Whoops\Util\SystemFacade::class)->shouldIgnoreMissing());

        /** @var Application|m\Mock $app */
        $app = $this->app = m::mock(Application::class)->makePartial();
        $app->shouldReceive('make')->passthru()->byDefault();
        $app->shouldReceive('get')->passthru()->byDefault();
        $app->__construct($basePath, $whoops);
        $app->registerNamespace('Test\Factory', 'Factory');

        $this->initDependencies();
    }

    protected function initDependencies()
    {
        // basic dependencies the app needs at any time
        $this->mocks['environment'] = m::mock(Environment::class, [$this->basePath])->makePartial();
        $this->app->instance('environment', $this->mocks['environment']);
        $this->app->alias('environment', Environment::class);

        $this->mocks['config'] = m::mock(Config::class)->makePartial();
        $this->mocks['config']->__construct($this->mocks['environment']);
        $this->app->instance('config', $this->mocks['config']);
        $this->app->alias('config', Config::class);

        /** @var Console|m\Mock $console */
        $console = $this->mocks['console'] = m::mock(Console::class)->makePartial();
        $console->__construct();
        $console->disableAnsi();
        $console->setStdout(fopen('php://memory', 'w'));
        $console->setStderr(fopen('php://memory', 'w'));
        $this->mocks['console']->shouldNotReceive(['read', 'readLine', 'readUntil']);
        $this->app->instance('console', $console);

        /** @var Logger|m\Mock $logger */
        $logger = $this->mocks['logger'] = m::mock(Logger::class)->makePartial();
        $logger->__construct('test');
        $logger->pushHandler(new StreamHandler('php://temp'));
        $this->app->instance('logger', $logger);

        $this->em = $this->mocks['entityManager'] = $this->ormInitMock([
            'tableNameTemplate' => '%short%s',
        ], 'pgsql');
        $pdo = $this->mocks['pdo'] = $this->em->getConnection();
        $this->app->instance('entityManager', $this->em);
        $this->app->instance('db', $pdo);

        $cache = $this->mocks['cache'] = m::mock(new Cache(new ArrayAdapter()));
        $this->app->instance('cache', $cache);

        /** @var Mailer|m\Mock $mailer */
        $mailer = $this->mocks['mailer'] = m::mock(Mailer::class, []);
        $mailer->shouldReceive('send')->with(m::type(Message::class))->byDefault();
        $this->app->instance('mailer', $mailer);

        $httpClient = $this->mocks['httpClient'] = m::mock(Client::class);
        $this->app->instance('httpClient', $httpClient);
    }

    /**
     * Overwrite a protected or private $property from $object to $value
     *
     * @param object $object
     * @param string $property
     * @param mixed  $value
     */
    protected static function setProtectedProperty($object, string $property, $value)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $property = (new \ReflectionClass($object))->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
    }

    /**
     * Get the value of a protected or private variable from $object
     *
     * @param $object
     * @param string $string
     * @return mixed
     */
    protected function getProtectedProperty($object, string $string)
    {
        $class = new \ReflectionClass($object);
        $property = $class->getProperty($string);
        $property->setAccessible(true);
        $value = $property->getValue($object);
        $property->setAccessible(false);
        return $value;
    }

    /**
     * @param User|array $user
     * @return User
     */
    protected function signIn($user = null): User
    {
        if (!$user instanceof User) {
            $data = is_array($user) ? $user : [];
            /** @var User|m\Mock $user */
            $user = $this->ormCreateMockedEntity(User::class, $this->ormAttributesToData(User::class, array_merge([
                'id' => 23,
                'name' => 'John Doe',
                'displayName' => 'john',
                'email' => 'john.doe@example.com',
                'accountStatus' => User::ACTIVATED,
                'created' => Carbon::now()->subMonth()->format('c'),
                'updated' => Carbon::now()->subDay()->format('c'),
            ], $data)));
        }

        $this->app->session->set('user', $user);

        return $user;
    }

    protected function requiresTrait(string $class, string $trait)
    {
        $reflection = new ReflectionClass($class);

        $traits = array_map(function (ReflectionClass $r) {
            return $r->getName();
        }, $reflection->getTraits());

        if (!in_array($trait, $traits)) {
            $this->markTestSkipped(sprintf('Class %s has to use %s for this test', $class, $trait));
        }
    }

    protected function resourcePath(string ...$path): string
    {
        return $this->app->environment->path('tests', 'resources', ...$path);
    }

    protected function randomId(): int
    {
        return mt_rand(ResultRepository::RANDOM_KEY_MIN, ResultRepository::RANDOM_KEY_MAX);
    }
}

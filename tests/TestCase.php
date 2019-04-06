<?php /** @noinspection PhpDocMissingThrowsInspection */

namespace Test;

use App\Application;
use App\Config;
use App\Environment;
use Hugga\Console;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use ORM\EntityManager;
use ORM\MockTrait;
use Whoops;

abstract class TestCase extends MockeryTestCase
{
    use MockTrait;

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

        /** @var Application|m\Mock $app */
        $app = $this->app = m::mock(Application::class)->makePartial();
        $app->__construct($basePath);

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

        /** @var Whoops\Run|m\Mock $whoops */
        $whoops = $this->mocks['whoops'] = m::mock($this->app->get('whoops'));
        $this->app->instance('whoops', $whoops);
        $whoops->unregister();
        $whoops->shouldReceive('register')->andReturnSelf()->byDefault();
        
        /** @var Console|m\Mock $console */
        $console = $this->mocks['console'] = m::mock(Console::class)->makePartial();
        $console->__construct();
        $console->disableAnsi();
        $console->setStdout(fopen('php://memory', 'w'));
        $console->setStderr(fopen('php://memory', 'w'));
        $this->mocks['console']->shouldNotReceive(['read', 'readLine', 'readUntil']);
        $this->app->instance('console', $console);

        $this->em = $this->mocks['entityManager'] = $this->ormInitMock([
            'tableNameTemplate' => '%short%s',
        ], 'pgsql');
        $this->mocks['pdo'] = $this->em->getConnection();
        $this->app->instance('entityManager', $this->em);
    }

    /**
     * @param callable ...$bootstrappers
     */
    protected function bootstrap(callable ...$bootstrappers)
    {
        foreach ($bootstrappers as $bootstrapper) {
            call_user_func($bootstrapper, $this->app);
        }
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
    protected function getProtectedVar($object, string $string)
    {
        $class = new \ReflectionClass($object);
        $property = $class->getProperty($string);
        $property->setAccessible(true);
        $value = $property->getValue($object);
        $property->setAccessible(false);
        return $value;
    }
}

<?php

namespace App;

use App\Service\Exception\LogHandler;
use Http\Response;
use Hugga\Console;
use Monolog\Logger;
use ORM\EntityManager;
use Syna\Factory;
use Verja\Gate;
use Whoops;

/**
 * Class Application
 *
 * @package App
 *
 * @method static Application app()
 * @method static Config config()
 * @method static Console console()
 * @method static EntityManager entityManager()
 * @method static Environment environment()
 * @method static Factory views()
 * @method static Logger logger()
 * @method static Gate verja()
 * @property-read Application $app
 * @property-read Config $config
 * @property-read Console $console
 * @property-read EntityManager $entityManager
 * @property-read Environment $environment
 * @property-read Factory $views
 * @property-read Logger $logger
 * @property-read Gate $verja
 */
class Application extends \Riki\Application
{
    /** @var Whoops\Run */
    protected $whoops;

    public function __construct(string $basePath)
    {
        parent::__construct($basePath);

        // bootstrap the application
        $this->initWhoops();
    }

    protected function initDependencies()
    {
        parent::initDependencies();

        // Register a namespace for factories
        $this->registerNamespace('App\Factory', 'Factory');

        // Register Whoops\Run under whoops
        $this->share('whoops', Whoops\Run::class);
    }


    /**
     * @return bool
     */
    public function initWhoops()
    {
        /** @var Whoops\Run $whoops */
        $whoops = $this->get('whoops');
        $whoops->register();
        $this->setErrorHandlers(...$this->getErrorHandlers());
        return true;
    }

    public function run(\Riki\Kernel $kernel, ...$args)
    {
        if ($kernel instanceof Kernel) {
            $this->setErrorHandlers(...$kernel->getErrorHandlers($this), ...$this->getErrorHandlers());
        }

        $result = parent::run($kernel, ...$args);

        if ($kernel instanceof Kernel) {
            // @todo this should be shift and unshift of kernels error handlers but there is only push and pop
            $this->setErrorHandlers(...$this->getErrorHandlers());
        }

        return $result;
    }

    protected function getErrorHandlers()
    {
        return [new LogHandler()];
    }

    protected function setErrorHandlers(...$handlers)
    {
        /** @var Whoops\Run $whoops */
        $whoops = $this->get('whoops');
        $whoops->clearHandlers();
        foreach ($handlers as $handler) {
            $whoops->pushHandler($handler);
        }
    }
}

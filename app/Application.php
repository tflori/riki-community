<?php

namespace App;

use App\Model\Gate;
use App\Model\Mail;
use App\Service\Exception\LogHandler;
use App\Service\Mailer;
use Hugga\Console;
use Monolog\Logger;
use NbSessions\SessionInstance;
use ORM\EntityManager;
use Psr\SimpleCache\CacheInterface;
use Redis;
use Syna\Factory;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Whoops;

/**
 * Application container that holds all instances and provides dependencies.
 *
 * @method static Application app()
 * @method static CacheInterface cache()
 * @method static Config config()
 * @method static Console console()
 * @method static CssToInlineStyles cssInliner()
 * @method static EntityManager entityManager()
 * @method static Environment environment()
 * @method static Factory views()
 * @method static Gate gate(array $fields = [], array $messages = [])
 * @method static Logger logger()
 * @method static Mail mail(string $name, array $data = [])
 * @method static Mailer mailer()
 * @method static Redis Redis()
 * @method Static SessionInstance session()
 * @property-read Application $app
 * @property-read CacheInterface $cache
 * @property-read Config $config
 * @property-read Console $console
 * @property-read CssToInlineStyles $cssInliner
 * @property-read EntityManager $entityManager
 * @property-read Environment $environment
 * @property-read Factory $views
 * @property-read Gate $gate
 * @property-read Logger $logger
 * @property-read Mailer $mailer
 * @property-read Redis $redis
 * @property-read SessionInstance $session
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

        // Register shared instances / classes
        $this->share('whoops', Whoops\Run::class);
        $this->share('cssInliner', CssToInlineStyles::class);
        $this->instance('entityManager', new EntityManager([
            EntityManager::OPT_CONNECTION => $this->config->dbConfig,
            'tableNameTemplate' => '%short%s',
        ]));
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

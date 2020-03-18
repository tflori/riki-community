<?php

namespace App;

use App\Model\Gate;
use App\Model\Mail;
use App\Service\Exception\LogHandler;
use App\Service\Mailer;
use GuzzleHttp\Client;
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
 * @method static Client httpClient()
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
 * @method static SessionInstance session()
 * @method static Whoops\Run whoops()
 * @property-read Application $app
 * @property-read CacheInterface $cache
 * @property-read Client $httpClient
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
 * @property-read Whoops\Run $whoops
 */
class Application extends \Riki\Application
{
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
        $this->share('cssInliner', CssToInlineStyles::class);
        $this->instance('whoops', $this->make(Whoops\Run::class, $this->make(Whoops\Util\SystemFacade::class)));
        $this->instance('entityManager', new EntityManager([
            EntityManager::OPT_CONNECTION => $this->config->dbConfig,
            'tableNameTemplate' => '%short%s',
        ]));
    }

    protected function initWhoops()
    {
        $this->whoops->register();
        $this->resetErrorHandlers();
    }

    public function run(\Riki\Kernel $kernel, ...$args)
    {
        if ($kernel instanceof Kernel) {
            foreach ($kernel->getErrorHandlers() as $handler) {
                $this->whoops->appendHandler($handler);
            }
        }

        $result = parent::run($kernel, ...$args);

        $this->resetErrorHandlers();

        return $result;
    }

    protected function resetErrorHandlers()
    {
        $this->whoops->clearHandlers();
        $this->whoops->appendHandler($this->make(LogHandler::class));
    }
}

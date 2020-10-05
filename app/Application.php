<?php

namespace App;

use App\Model\Gate;
use App\Model\Mail;
use App\Model\Request;
use App\Service\Authorization;
use App\Service\Cache;
use App\Service\Exception\LogHandler;
use App\Service\Mailer;
use App\Service\Url;
use DependencyInjector\Factory\NamespaceFactory;
use GuzzleHttp\Client;
use Hugga\Console;
use Monolog\Logger;
use NbSessions\SessionInstance;
use ORM\EntityManager;
use PDO;
use Redis;
use Syna\Factory;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Whoops;

/**
 * Application container that holds all instances and provides dependencies.
 *
 * @method static Application app()
 * @method static Authorization auth();
 * @method static Cache cache()
 * @method static Client httpClient()
 * @method static Config config()
 * @method static Console console()
 * @method static CssToInlineStyles cssInliner()
 * @method static EntityManager em()
 * @method static EntityManager entityManager()
 * @method static Environment environment()
 * @method static Factory views()
 * @method static Gate gate(array $fields = [], array $messages = [])
 * @method static Logger logger()
 * @method static Mail mail(string $name, array $data = [])
 * @method static Mailer mailer()
 * @method static PDO db()
 * @method static Redis redis()
 * @method static Request request()
 * @method static SessionInstance session()
 * @method static Url url()
 * @property-read Application $app
 * @property-read Authorization $auth
 * @property-read Cache $cache
 * @property-read Client $httpClient
 * @property-read Config $config
 * @property-read Console $console
 * @property-read CssToInlineStyles $cssInliner
 * @property-read EntityManager $em
 * @property-read EntityManager $entityManager
 * @property-read Environment $environment
 * @property-read Factory $views
 * @property-read Gate $gate
 * @property-read Logger $logger
 * @property-read Mailer $mailer
 * @property-read PDO $db
 * @property-read Redis $redis
 * @property-read Request $request
 * @property-read SessionInstance $session
 * @property-read Url $url
 */
class Application extends \Riki\Application
{
    /** @var Whoops\Run */
    protected $whoops;

    public function __construct(string $basePath, Whoops\Run $whoops = null)
    {
        $this->whoops = $whoops;
        parent::__construct($basePath);

        // bootstrap the application
        $this->initWhoops();
    }

    protected function initDependencies()
    {
        parent::initDependencies();

        // Register a namespace for factories
        $this->registerNamespace('App\Factory', 'Factory');

        // share some base classes
        $this->share('cssInliner', CssToInlineStyles::class);

        // initialize namespace factories
        $controllerFactory = new NamespaceFactory($this->app, Http\Controller::class);
        $controllerFactory->addArguments($this->app);
        $this->app->addPatternFactory($controllerFactory);
        $middlewareFactory = new NamespaceFactory($this->app, Http\Middleware::class);
        $middlewareFactory->addArguments($this->app);
        $this->app->addPatternFactory($middlewareFactory);
        $helperFactory = new NamespaceFactory($this->app, View\Helper::class);
        $helperFactory->addArguments($this->app)->share();
        $this->app->addPatternFactory($helperFactory);

        // initialize entity manager
        EntityManager::setResolver(function () {
            return $this->entityManager;
        });
    }

    protected function initWhoops()
    {
        $this->whoops || $this->whoops = $this->make(Whoops\Run::class);
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

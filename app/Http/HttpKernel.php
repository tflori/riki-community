<?php

namespace App\Http;

use App\Application;
use App\Http\Controller\ErrorController;
use App\Http\Router\MiddlewareDataGenerator;
use App\Http\Router\MiddlewareRouteCollector;
use App\Http\Router\MiddlewareRouter;
use App\Model\Request;
use FastRoute;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Whoops\Handler\Handler;
use Whoops\Handler\PrettyPageHandler;

class HttpKernel extends \App\Kernel
{
    const CONTROLLER_NAMESPACE = 'App\Http\Controller';

    /** @var Request */
    protected static $lastRequest;

    /** @var Request[] */
    protected static $requests = [];

    /** @var MiddlewareRouter */
    protected $router;

    public function handle(Request $request = null): ResponseInterface
    {
        if (!$request) {
            // During tests we don't create a request object from super globals
            // @codeCoverageIgnoreStart
            $request = Request::fromGlobals();
            // @codeCoverageIgnoreEnd
        }

        $this->addRequest($request);

        try {
            $handlers = [];
            $arguments = [];
            $result = $this->getRouter()->dispatch($request->getMethod(), $request->getRelativePath());
            switch ($result[0]) {
                case FastRoute\Dispatcher::FOUND:
                    list(, $handlers, $arguments) = $result;
                    break;

                case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                    list(, $allowedMethods, $handlers) = $result;
                    $handlers[] = [ErrorController::class, 'methodNotAllowed'];
                    $arguments = ['allowedMethods' => $allowedMethods];
                    break;

                case FastRoute\Dispatcher::NOT_FOUND:
                    list(, $handlers) = $result;
                    $handlers[] = [ErrorController::class, 'notFound'];
                    break;
            }

            if (!empty($arguments)) {
                $request = $request->withAttribute('arguments', $arguments);
            }

            return Application::app()
                ->make(Dispatcher::class, $handlers, [$this, 'getHandler'])
                ->handle($request);
        } finally {
            $this->popLastRequest();
        }
    }

    /**
     * Get a RequestHandler or Middleware for $handler
     *
     * @param string|array|callable $handler
     * @return RequestHandlerInterface|MiddlewareInterface|callable
     */
    public function getHandler($handler)
    {
        if (!is_string($handler) && !is_callable($handler)) {
            throw new \InvalidArgumentException(
                '$handler has to be a callable, a string in form "method@Controller" or a class name'
            );
        }

        if (is_string($handler)) {
            if (is_callable($handler) && self::isStatic($handler)) {
                return $handler;
            }

            $class = $handler;
            if (($pos = strpos($handler, '@')) >= 1) {
                $class = substr($handler, $pos + 1);
                $method = substr($handler, 0, $pos);
            }
        } elseif (is_array($handler)) {
            if (is_object($handler[0]) || self::isStatic($handler)) {
                return $handler;
            }

            list($class, $method) = $handler;
        } else {
            return $handler;
        }

        if (!class_exists($class)) {
            if (!class_exists(self::CONTROLLER_NAMESPACE . '\\' . $class)) {
                throw new \InvalidArgumentException(sprintf('Class %s not found', $class));
            }
            $class = self::CONTROLLER_NAMESPACE . '\\' . $class;
        }

        if (!isset($method)) {
            return $this->app->make($class);
        }

        return new RequestHandler($this->app, $class, $method);
    }

    public function getErrorHandlers(): array
    {
        if ($this->app->environment->canShowErrors()) {
            $handler = new PrettyPageHandler();
            if ($this->app->config->env('PROJECT_PATH')) {
                /** @codeCoverageIgnore no way to test opening files in editor in ci */
                $handler->setEditor(function (string $file, int $line) {
                    $file =preg_replace(
                        '~^' . $this->app->getBasePath() . '~',
                        $this->app->config->env('PROJECT_PATH'),
                        $file
                    );
                    $url = sprintf(
                        $this->app->config->env('EDITOR_URL', 'http://localhost:63342/api/file/?file=%s&line=%d'),
                        $file,
                        $line
                    );
                    return parse_url($url, PHP_URL_SCHEME) === 'http' ? ['url' => $url, 'ajax' => true] : $url;
                });
            }
            return [$handler];
        } else {
            return [function ($exception = null) {
                $request = ($this->app->request ?? Request::fromGlobals())
                    ->withAttribute('arguments', ['exception' => $exception]);
                /** @var ErrorController $errorController */
                $errorController = $this->getHandler([ErrorController::class, 'unexpectedError']);
                $errorController->handle($request)->send();
                return Handler::QUIT;
            }];
        }
    }

    public function getRouter(): MiddlewareRouter
    {
        if (!$this->router) {
            // @todo implement caching for $routeCollector->getData()
            $dataGenerator = $this->app->make(MiddlewareDataGenerator::class);
            $routeParser = $this->app->make(FastRoute\RouteParser\Std::class);
            $routeCollector = $this->app->make(MiddlewareRouteCollector::class, $routeParser, $dataGenerator);
            self::collectRoutes($this->app, $routeCollector);
            $this->router = $this->app->make(MiddlewareRouter::class, $routeCollector->getData());
        }

        return $this->router;
    }

    /**
     * Get the current request or generate one
     *
     * @return Request|null
     */
    public static function currentRequest(): ?Request
    {
        return self::$requests[count(self::$requests) - 1] ?? null;
    }

    /** Add the request to our stack */
    protected function addRequest(Request $request)
    {
        self::$requests[] = $request;
    }

    /** Pop the last request out of the stack */
    protected function popLastRequest()
    {
        array_pop(self::$requests);
    }

    protected static function collectRoutes(Application $app, MiddlewareRouteCollector $router)
    {
        // @todo maybe you want to load different route files or collect them from annotations..
        include __DIR__ . '/routes.php';
        $router->addGroup('/', function () {
            // this ensures that the handler on root level are loaded for non matching routes
        });
    }

    /**
     * Check if $callable is static
     *
     * A non static method will result in is_callable($callable) === true even when it is protected. This method
     * checks if the callable is really static and callable.
     *
     * @param callable $callable
     * @return bool
     */
    protected static function isStatic($callable): bool
    {
        if (is_array($callable)) {
            list($class, $method) = $callable;
        } elseif (is_string($callable) && strpos($callable, '::', 1) !== false) {
            list($class, $method) = explode('::', $callable);
        } else {
            return true;
        }

        if (class_exists($class)) {
            $classReflection = new \ReflectionClass($class);
            $methodReflection = $classReflection->getMethod($method);
            if (!$classReflection->isAbstract() &&
                $methodReflection->isStatic() &&
                $methodReflection->isPublic() &&
                !$methodReflection->isAbstract()
            ) {
                return true;
            }
        }

        return false;
    }
}

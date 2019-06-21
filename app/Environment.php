<?php

namespace App;

/**
 * Fallback and default environment
 *
 * Typically that contains information about the environment (development, production etc.) like paths, if errors can
 * be shown or the base url to this environment.
 *
 * The App\Environment\* classes inherit this class and are automatically chosen by the APP_ENV environment variable.
 *
 * @codeCoverageIgnore trivial code
 */
class Environment extends \Riki\Environment
{
    public function canShowErrors()
    {
        return false;
    }

    public function getConfigCachePath(): string
    {
        return $this->cachePath('config.spo');
    }

    public function storagePath(string ...$path): string
    {
        return $this->path('storage', ...$path);
    }

    public function resourcePath(string ...$path): string
    {
        return $this->path('resources', ...$path);
    }

    public function cachePath(string ...$path): string
    {
        return $this->storagePath('cache', ...$path);
    }

    public function logPath(string ...$path): string
    {
        return $this->storagePath('logs', ...$path);
    }

    public function viewPath(string ...$path): string
    {
        return $this->resourcePath('views', ...$path);
    }

    public function publicPath(string ...$path): string
    {
        return $this->path('public', ...$path);
    }

    public function path(string ...$path): string
    {
        array_unshift($path, $this->getBasePath());
        return implode(DIRECTORY_SEPARATOR, $path);
    }

    public function url(string ...$path): string
    {
        array_unshift(
            $path,
            ($this->isSslSecured() ? 'https' : 'http') . '://' .
            ($_SERVER['HTTP_HOST'] ?? 'localhost')
        );
        return implode('/', $path);
    }

    public function cookieOptions()
    {
        return [
            'path'     => '/',
            'domain'   => null,
            'secure'   => $this->isSslSecured(),
            'httponly' => true,
        ];
    }

    protected function isSslSecured()
    {
        return false;
    }
}

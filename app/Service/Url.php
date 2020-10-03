<?php

namespace App\Service;

use App\Model\Request;
use GuzzleHttp\Psr7\Uri;
use function GuzzleHttp\Psr7\build_query;

class Url
{
    /** @var Request|null */
    protected $request;

    /** @var string */
    protected $base;

    /** @var string */
    protected $hostAndScheme;

    /** @var string */
    protected $fallbackUrl;

    public function __construct(string $fallbackUrl, ?Request $request)
    {
        $this->fallbackUrl = $fallbackUrl;
        $this->request = $request;
    }

    public function absolute(string $path, array $params = [])
    {
        return $this->getSchemeAndHost() . $this->getBase() . $this->buildUri($path, $params);
    }

    public function local(string $path, array $params = [])
    {
        return $this->getBase() . $this->buildUri($path, $params);
    }

    protected function buildUri(string $path, array $params = [])
    {
        $uri = ltrim($path, '/');
        if (!empty($params)) {
            $uri .= '?' . build_query($params);
        }

        return $uri;
    }

    protected function getBase(): string
    {
        if (!$this->base) {
            if (!$this->request) {
                $uri = new Uri($this->fallbackUrl);
                $this->base = rtrim($uri->getPath(), '/') . '/';
            } else {
                $this->base = rtrim($this->request->getBase(), '/') . '/';
            }
        }

        return $this->base;
    }

    protected function getSchemeAndHost(): string
    {
        if (!$this->hostAndScheme) {
            if (!$this->request) {
                $uri = new Uri($this->fallbackUrl);
                $scheme = $uri->getScheme();
                $host = $uri->getAuthority();
            } else {
                $scheme = $this->request->getProtocol();
                $host = $this->request->getUri()->getAuthority();
            }

            $this->hostAndScheme = sprintf('%s://%s', $scheme, $host);
        }

        return $this->hostAndScheme;
    }
}

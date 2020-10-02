<?php

namespace App\Model;

use App\Application;
use App\Exception\InvalidJsonBody;
use App\Service\IpHelper;
use App\Service\ValidatorMessages;
use InvalidArgumentException;
use Tal\ServerRequest;

class Request extends ServerRequest
{
    const REAL_IP_HEADER = ['X-Real-Ip', 'X-Forwarded-For'];

    /** @var array */
    protected $input;

    /** @codeCoverageIgnore */
    public function __clone()
    {
        $this->input = null;
    }

    /** @codeCoverageIgnore */
    public function __get($name)
    {
        $this->get($name);
    }

    /**
     * Get the IP of the client
     *
     * If we run behind a reversed proxy make sure to set the TRUSTED_PROXIES variable accordingly.
     *
     * Returns the x-real-ip and x-forwarded-for header when the proxy is trusted.
     *
     * @return string
     */
    public function getIp(): string
    {
        $remoteAddr = $this->serverParams['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!$this->isTrustedForward()) {
            return $remoteAddr;
        }

        foreach (static::REAL_IP_HEADER as $header) {
            if ($this->hasHeader($header)) {
                $forwardedFor = array_reverse(array_map('trim', explode(',', $this->getHeader($header)[0])));
                return $forwardedFor[0];
            }
        }

        return $remoteAddr;
    }

    /**
     * Get the currently used protocol
     *
     * If we run behind a reversed proxy make sure to set the TRUSTED_PROXIES variable accordingly.
     *
     * Returns the x-forwarded-proto header when the proxy is trusted.
     *
     * @return string
     */
    public function getProtocol(): string
    {
        // any non empty value means it is using https except the value 'off'
        $currentProtocol = ($this->serverParams['HTTPS'] ?? 'off') !== 'off' ? 'https' : 'http';

        if (!$this->isTrustedForward()) {
            return $currentProtocol;
        }

        if ($this->hasHeader('X-Forwarded-Proto')) {
            return $this->getHeader('X-Forwarded-Proto')[0];
        }

        return $currentProtocol;
    }

    /**
     * Check if the proxy is a trusted proxy.
     *
     * Returns whether or not the direct client ip ($_SERVER['REMOTE_ADDR']) matches against one of the defined proxies
     * in $config->trustedProxies.
     *
     * $config->trustedProxies is an array of ip addresses, address ranges or a partial reg ex pattern.
     *
     * Examples:
     * ```php
     * $config->trustedProxies = [
     *     '192.168.0.1', // ipv4 of our reverse proxy (only one proxy)
     *     '42.42.42.64/28', // ipv4 subnet (the proxy comes from this subnet)
     *     '42.42.42.\d+', // ipv4 reg ex matching any ip from a /24 subnet
     *     'fe80::/64', // link local ipv6 range
     *     'fe80:(:|(0:)+)+[0-9a-f:]+', // unsure if this works - ipv6 reg ex patterns are terrible
     * ];
     *```
     *
     * @return bool
     */
    public function isTrustedForward(): bool
    {
        $config = Application::config();
        if (empty($config->trustedProxies)) {
            return false;
        }

        /** @var IpHelper $ipHelper */
        $ipHelper = Application::app()->make(IpHelper::class);
        foreach ($config->trustedProxies as $ipRange) {
            if ($ipHelper->isInRange($this->serverParams['REMOTE_ADDR'] ?? '127.0.0.1', $ipRange)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Was the request an ssl secured request
     *
     * You might want to return an error response when the request was not secured via ssl.
     *
     * @return bool
     */
    public function isSslSecured(): bool
    {
        return $this->getProtocol() === 'https';
    }

    /**
     * Get the preferred value from accept header
     *
     * Limit to $possible if necessary. Return $default if nothing possible accepted or header omitted.
     *
     * @param array  $possible
     * @return string|null
     */
    public function getPreferredContentType(array $possible): ?string
    {
        if (!$this->hasHeader('Accept')) {
            return $possible[0];
        }

        $accepted = [];
        foreach (explode(',', $this->getHeader('Accept')[0]) as $item) {
            $parameters = explode(';', trim($item));
            $value = trim(array_shift($parameters));

            $q = 1;
            foreach ($parameters as $parameter) {
                $parameter = trim($parameter);
                if (substr($parameter, 0, 2) !== 'q=' || !is_numeric(substr($parameter, 2))) {
                    continue;
                }
                $q = (double)substr($parameter, 2);
            }

            // hack for stable sorting by quality: repeated quality has 0.0001 less quality
            while (isset($accepted[(string)$q])) {
                $q -= 0.0001;
            }
            $accepted[(string)$q] = $value;
        }

        krsort($accepted); // sort by quality
        $accepted = array_intersect($accepted, $possible);
        return array_shift($accepted) ?? $possible[0];
    }

    /**
     * Check parameters with validation
     *
     * Returns true when valid and false when not. $data will be filled with validated data (when valid) and $errors
     * will be filled with Verja\Error (when invalid).
     *
     * $source has to be an array or an existing getter (query, post, json).
     *
     * Usage:
     * ```php
     *   if (!$this->validate(['name' => ['required']], 'post', $data, $errors)) {
     *     // fail and show $errors
     *   }
     *   // use $data
     * ```
     *
     * @param array        $fields
     * @param string|array $source
     * @return array
     * @throws InvalidArgumentException
     */
    public function validate(array $fields, $source = 'query', array $messages = [])
    {
        if (!is_array($source)) {
            if (!method_exists($this, 'get' . ucfirst($source))) {
                throw new InvalidArgumentException(
                    '$source has to be an array or an existing getter (query, post, json)'
                );
            }
            $source = call_user_func([$this, 'get' . ucfirst($source)]);
        }
        $gate = Application::gate($fields, $messages);
        $valid = $gate->validate($source);

        $data = $valid ? $gate->getData() : [];
        $errors = !$valid ? $gate->getErrorMessages() : [];
        return [$valid, $data, $errors];
    }

    public function get(string $key = null, $default = null)
    {
        if (!$this->input) {
            try {
                $body = $this->getJson();
            } catch (InvalidJsonBody $e) {
                $body = $this->getPost();
            }

            $this->input = is_array($body) ? array_merge($this->getQuery(), $body) : $this->getQuery();
        }

        if (!$key) {
            return $this->input;
        }

        return $this->input[$key] ?? $default;
    }

    /**
     * Get all params or the parameter $key from query
     *
     * @param string $key
     * @param mixed $default
     * @return array|mixed
     */
    public function getQuery(string $key = null, $default = null)
    {
        if (!$key) {
            return $this->getQueryParams();
        }

        return $this->getQueryParams()[$key] ?? $default;
    }

    /**
     * Get all params or the parameter $key from post body
     *
     * @param string $key
     * @param mixed $default
     * @return array|mixed
     */
    public function getPost(string $key = null, $default = null)
    {
        if (!$key) {
            return $this->getParsedBody();
        }

        return $this->getParsedBody()[$key] ?? $default;
    }

    /**
     * Get the parsed json from request body
     *
     * Warning: $assoc defaults to true in this method.
     *
     * @param bool $assoc
     * @param int  $depth
     * @param int  $options
     * @see json_decode()
     * @return mixed
     */
    public function getJson(bool $assoc = true, int $depth = 512, int $options = 0)
    {
        $data = json_decode((string)$this->getBody(), $assoc, $depth, $options);

        if ($data === null && (string)$this->getBody() !== 'null') {
            throw new InvalidJsonBody(sprintf('Invalid json provided in body: \'%s\'', $this->getBody()));
        }

        return $data;
    }
}

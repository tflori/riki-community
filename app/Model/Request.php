<?php

namespace App\Model;

use App\Application;
use App\Service\ValidatorMessages;
use InvalidArgumentException;
use Tal\ServerRequest;

class Request extends ServerRequest
{
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
     *   list($valid, $data) = $this->validate(['name' => ['required']], 'post');
     *   if (!$valid) {
     *     // fail show validation messages from $data
     *   }
     *   // use $data
     * ```
     *
     * @param array        $fields
     * @param string|array $source
     * @param array        $messages
     * @return array
     */
    public function validate(array $fields, $source = 'query', array $messages = []): array
    {
        if (!is_array($source)) {
            if (!method_exists($this, 'get' . ucfirst($source))) {
                throw new InvalidArgumentException(
                    '$source has to be an array or an existing getter (query, post, json)'
                );
            }
            $source = call_user_func([$this, 'get' . ucfirst($source)]);
        }
        $gate = Application::verja();
        $gate->accepts($fields);

        if ($gate->validate($source)) {
            return [true, $gate->getData()];
        }

        $messages = Application::app()->make(ValidatorMessages::class, $gate->getErrors(), $messages);
        return [false, $messages->getMessages()];
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
            throw new InvalidArgumentException('Invalid json provided in body');
        }

        return $data;
    }
}

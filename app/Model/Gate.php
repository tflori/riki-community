<?php

namespace App\Model;

use Verja;

class Gate extends Verja\Gate
{
    protected $messages = [];

    /** @codeCoverageIgnore  */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /** @codeCoverageIgnore */
    public function setMessages(array $messages): Gate
    {
        $this->messages = $messages;
        return $this;
    }

    public function getErrorMessages(array $messages = null): array
    {
        if ($messages) {
            $this->messages = $messages;
        }

        $errorMessages = [];
        foreach ($this->getErrors() as $field => $errors) {
            $errorMessages[$field] = [];
            /** @var Verja\Error $error */
            foreach ($errors as $error) {
                $errorMessages[$field][] = $this->getMessage($field, $error);
            }
        }

        return $errorMessages;
    }

    protected function getMessage(string $field, Verja\Error $error): string
    {
        if (isset($this->messages[$field . '.' . $error->key])) {
            $message = $this->messages[$field . '.' . $error->key];
        } elseif (isset($this->messages[$error->key])) {
            $message = $this->messages[$error->key];
        } else {
            return ucfirst($error->message);
        }

        return preg_replace_callback(
            '/(^|[^%])%([a-zA-Z0-9_-]+)\$(.*?[bcdeEfFgGosuxX])/',
            function ($match) use ($error) {
                if (isset($error->parameters[$match[2]])) {
                    $parameter = !is_scalar($error->parameters[$match[2]]) ?
                        $this->stringifyParameter($error->parameters[$match[2]]) : $error->parameters[$match[2]];
                    return $match[1] . sprintf('%' . $match[3], $parameter);
                }

                return $match[1];
            },
            $message
        );
    }

    protected function stringifyParameter($value): string
    {
        switch (gettype($value)) {
            case 'object':
                return method_exists($value, '__toString') ? $value->__toString() : get_class($value);

            case 'array':
                return count(array_filter(array_keys($value), function ($key) {
                    return !is_numeric($key);
                })) ? json_encode($value) : implode(',', array_values($value));

            default:
                return gettype($value);
        }
    }
}

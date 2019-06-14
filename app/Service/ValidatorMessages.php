<?php

namespace App\Service;

use Verja\Error;

class ValidatorMessages
{
    /** @var Error[] */
    protected $errors;

    /** @var string[] */
    protected $messages = [];

    /**
     * ValidatorMessages constructor.
     *
     * @param Error[][] $errors
     * @param array     $messages
     */
    public function __construct(array $errors, array $messages = [])
    {
        $this->errors = $errors;
        $this->messages = array_merge($this->messages, $messages);
    }

    public function getMessages()
    {
        $messages = [];
        foreach ($this->errors as $field => $errors) {
            $messages[$field] = [];
            /** @var Error $error */
            foreach ($errors as $error) {
                $messages[$field][] = $this->getMessage($field, $error);
            }
        }

        return $messages;
    }

    protected function getMessage(string $field, Error $error): string
    {
        if (isset($this->messages[$field . '.' . $error->key])) {
            $message = $this->messages[$field . '.' . $error->key];
        } elseif (isset($this->messages[$error->key])) {
            $message = $this->messages[$error->key];
        } else {
            return ucfirst($error->message);
        }

        return preg_replace_callback(
            '/(^|[^%])%([a-zA-Z0-9_-]+)\$(.*[bcdeEfFgGosuxX])/',
            function ($match) use ($error) {
                if (isset($error->parameters[$match[2]])) {
                    return $match[1] . sprintf('%' . $match[3], $error->parameters[$match[2]]);
                }

                return $match[1];
            },
            $message
        );
    }
}

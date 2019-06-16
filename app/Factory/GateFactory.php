<?php

namespace App\Factory;

use App\Model\Gate;
use Psr\Container\ContainerInterface;
use Verja\Filter;
use Verja\Validator;

class GateFactory extends AbstractFactory
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        Filter::registerNamespace(\App\Filter::class);
        Validator::registerNamespace(\App\Validator::class);
    }

    /**
     * This method builds the instance.
     *
     * @return Gate
     */
    protected function build(array $fields = [], array $messages = [])
    {
        $validator = new Gate();
        $validator->accepts($fields);
        $validator->setMessages($messages);
        return $validator;
    }
}

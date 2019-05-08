<?php

namespace App\Factory;

use Verja\Filter;
use Verja\Gate;
use Verja\Validator;

class VerjaFactory extends AbstractFactory
{
    /**
     * This method builds the instance.
     *
     * @return mixed
     */
    protected function build()
    {
        Filter::registerNamespace(\App\Filter::class);
        Validator::registerNamespace(\App\Validator::class);
        return new Gate();
    }
}

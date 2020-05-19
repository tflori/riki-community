<?php

namespace App\Factory;

use App\Http\HttpKernel;

class RequestFactory extends AbstractFactory
{
    protected function build()
    {
        return HttpKernel::currentRequest();
    }
}

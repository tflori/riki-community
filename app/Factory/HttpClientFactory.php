<?php

namespace App\Factory;

use GuzzleHttp\Client;

/**
 * @codeCoverageIgnore trivial
 */
class HttpClientFactory extends AbstractFactory
{
    /**
     * This method builds the instance.
     *
     * @return Client
     */
    protected function build()
    {
        return new Client([
            'timeout' => 2.0,
        ]);
    }
}

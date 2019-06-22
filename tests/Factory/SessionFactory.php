<?php

namespace Test\Factory;

use Test\Mocks\FakeSession;

class SessionFactory extends \App\Factory\SessionFactory
{
    protected function build()
    {
        return new FakeSession();
    }
}

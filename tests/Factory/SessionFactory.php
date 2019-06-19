<?php

namespace Test\Factory;

use Mockery as m;
use NbSessions\SessionInstance;

class SessionFactory extends \App\Factory\SessionFactory
{
    protected function build()
    {
        $session = m::mock(SessionInstance::class);
        $session->shouldReceive('get')->withAnyArgs()->andReturn(null)->byDefault();
        $session->shouldReceive('set')->withAnyArgs()->andReturnSelf()->byDefault();
        return $session;
    }
}

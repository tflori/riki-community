<?php

namespace Test\Cli;

use App\Cli\CliKernel;
use GetOpt\GetOpt;
use Hugga\Console;
use Mockery as m;

class TestCase extends \Test\TestCase
{
    protected function start(...$arguments)
    {
        $kernel = new CliKernel($this->app);

        $stdout = fopen('php://memory', 'w+');
        $stderr = fopen('php://memory', 'w+');
        $this->mocks['console']->setStdout($stdout);
        $this->mocks['console']->setStderr($stderr);

        $returnVar = $this->app->run($kernel, $arguments);

        rewind($stdout);
        rewind($stderr);

        return [
            'returnVar' => $returnVar,
            'output' => stream_get_contents($stdout),
            'errors' => stream_get_contents($stderr)
        ];
    }

    protected function initDependencies()
    {
        parent::initDependencies();

        $this->mocks['getOpt'] = m::mock(GetOpt::class)->makePartial();
        $this->app->instance(GetOpt::class, $this->mocks['getOpt']);
    }
}

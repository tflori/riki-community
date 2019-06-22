<?php

namespace Test\Mocks;

use NbSessions\SessionInstance;

class FakeSession extends SessionInstance
{
    public function __construct()
    {
        $this->name = 'test-session';
        self::$useCookies = false;
    }

    protected function init()
    {
    }

    protected function updateSession(array $data = [])
    {
        $_SESSION = $data;
        $this->data = $_SESSION;
    }

    public function destroy()
    {
        $_SESSION = [];
        $this->data = $_SESSION;
        $this->destroyed = true;
        return $this;
    }
}

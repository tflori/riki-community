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
        foreach ($data as $key => $val) {
            if ($val === null) {
                unset($_SESSION[$key]);
                // destroy the session when empty
                if (empty($_SESSION) && $this->destroyEmptySession) {
                    $this->destroy();
                    return;
                }
            } else {
                $_SESSION[$key] = $val;
            }
        }
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

<?php

namespace App\Model;

use Nette\Mail\Message;

class Mail extends Message
{
    public function __toString()
    {
        $from = $this->getHeader('From');
        $from = $from ? ' from ' . array_keys($from)[0] : '';

        $to = $this->getHeader('To');
        $to = $to ? ' to ' . implode(', ', array_keys($to)) : '';

        $subject = $this->getHeader('Subject');
        $subject = $subject ? sprintf(' with subject "%s"', $subject) : '';

        return 'eMail' . $from . $to . $subject;
    }
}

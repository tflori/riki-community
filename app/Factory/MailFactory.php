<?php

namespace App\Factory;

use App\Model\Mail;
use Parsedown;

class MailFactory extends AbstractFactory
{
    /**
     * @param string  $name
     * @param array $data
     *
     * @return Mail
     */
    protected function build(string $name = '', array $data = [])
    {
        // create a mail
        $mail = new Mail($this->container->config->email);

        // render the email body
        $view = $this->container->views->view('mail::' . $name);
        $markdown = $view->render($data);

        $layout = $this->container->views->view('layout::mail');
        $layout->setSections(array_merge($view->getSections(), ['content' => (new Parsedown())->parse($markdown)]));


        if ($subject = $view->section('subject')) {
            $mail->setSubject($subject);
        }
        return $mail->setBody($markdown)
            ->setHtmlBody($layout->render());
    }
}

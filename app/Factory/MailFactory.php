<?php

namespace App\Factory;

use App\Model\Mail;
use Parsedown;

class MailFactory extends AbstractFactory
{
    /**
     * @param string $name
     * @param array $data
     *
     * @return Mail
     */
    protected function build(string $name = '', array $data = [])
    {
        $config = $this->container->config;
        $environment = $this->container->environment;
        $views  = $this->container->views;
        $cssInliner = $this->container->cssInliner;

        // create a mail
        $mail   = new Mail($config->email['headers']);

        // render the email body
        $view     = $views->view('mail::' . $name);
        $markdown = $view->render(array_merge($data, ['texts' => $config->email['texts']]));

        $layout = $views->view('layout::mail');
        $layout->setSections(array_merge($view->getSections(), ['content' => (new Parsedown())->parse($markdown)]));


        if ($subject = $view->section('subject')) {
            $mail->setSubject($subject);
        }
        return $mail->setBody(strip_tags($markdown))
            ->setHtmlBody(
                $cssInliner->convert($layout->render()),
                $environment->publicPath()
            );
    }
}

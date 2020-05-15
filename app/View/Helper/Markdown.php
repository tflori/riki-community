<?php

namespace App\View\Helper;

class Markdown extends AbstractViewHelper
{
    public function __invoke($content = '')
    {
        return (new \Parsedown())->parse($content);
    }
}

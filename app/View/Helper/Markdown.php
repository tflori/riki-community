<?php

namespace App\View\Helper;

use Syna\ViewHelper\AbstractViewHelper;

class Markdown extends AbstractViewHelper
{
    public function __invoke($content = '')
    {
        return (new \Parsedown())->parse($content);
    }
}

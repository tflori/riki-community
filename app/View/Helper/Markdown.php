<?php

namespace App\View\Helper;

use Syna\ViewHelper\AbstractViewHelper;

class Markdown extends AbstractViewHelper
{
    public function __invoke($content = '')
    {
        return '<article class="markdown">' . (new \Parsedown())->parse($content) . '</article>';
    }
}

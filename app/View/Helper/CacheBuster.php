<?php

namespace App\View\Helper;

use App\Application;
use Syna\ViewHelper\AbstractViewHelper;

class CacheBuster extends AbstractViewHelper
{
    public function __invoke($path = '')
    {
        $filePath = Application::environment()->publicPath($path);

        if (!file_exists($filePath)) {
            return $path;
        }

        // @todo implement caching...
        $md5 = md5_file($filePath);
        return $path . '?_=' . substr($md5, 0, 10);
    }
}

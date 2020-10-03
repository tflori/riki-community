<?php

namespace App\View\Helper;

use App\Application;

class CacheBuster extends AbstractViewHelper
{
    public function __invoke($path = '')
    {
        $filePath = Application::environment()->publicPath($path);

        if (!file_exists($filePath)) {
            return $path;
        }

        $md5 = md5_file($filePath);
        return $this->app->url->local($path, ['_' => substr($md5, 0, 10)]);
    }
}

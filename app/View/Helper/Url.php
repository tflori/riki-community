<?php

namespace App\View\Helper;

class Url extends AbstractViewHelper
{
    /** @codeCoverageIgnore trivial */
    public function __invoke(string $path = '', array $params = [], $absolute = false)
    {
        if ($absolute) {
            return $this->app->url->absolute($path, $params);
        }

        return $this->app->url->local($path, $params);
    }
}

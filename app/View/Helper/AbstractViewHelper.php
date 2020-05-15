<?php

namespace App\View\Helper;

use App\Application;
use Syna\ViewHelper\AbstractViewHelper as BaseAbstractViewHelper;

abstract class AbstractViewHelper extends BaseAbstractViewHelper
{
    /** @var Application */
    protected $app;

    /**
     * AbstractViewHelper constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}

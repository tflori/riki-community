<?php

namespace App\Http\Controller;

use App\Application;
use App\Http\Concerns\GeneratesResponses;
use App\Model\Request;
use Exception;
use Tal\ServerResponse;
use Throwable;

abstract class AbstractController
{
    use GeneratesResponses;

    /** @var Application */
    protected $app;

    /** @var Request */
    protected $request;

    /**
     * AbstractController constructor.
     *
     * @param Application $app
     * @param Request     $request
     */
    public function __construct(Application $app, Request $request)
    {
        $this->app     = $app;
        $this->request = $request;
    }
}

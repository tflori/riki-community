<?php

use App\Application;
use App\Http\HttpKernel;
use Tal\Psr7Extended\ServerResponseInterface;

require __DIR__ . '/../vendor/autoload.php';

$app = new Application(realPath(__DIR__ . '/..'));
$kernel = new HttpKernel($app);

/** @var ServerResponseInterface $response */
$response = $app->run($kernel);
$response->send();

<?php
/**
 * Define your routes here.
 *
 * Handlers should always be class names, callable like ['class', 'method'] or the form 'action@Controller'. You can
 * also use closure handlers but keep in mind that they can not be serialized and therefore you are not able to cache
 * routes.
 *
 * You may want to split this file with groups and includes or load routes from annotations, or from several files.
 *
 * @see \App\Http\HttpKernel::collectRoutes
 */

use App\Application;
use App\Http\Router\MiddlewareRouteCollector;

/** @var MiddlewareRouteCollector $router */
$r = $router;

$r->get('/', 'getHome@HomeController');
$r->get('/home', 'getHome@HomeController');

$r->post('/user', 'createUser@UserController');
//$r->post('/user/activate', Authenticated::class, 'activate@UserController');
//$r->get('/user/activate/{token}', 'activateByToken@UserController');
//
//$r->post('/auth', 'authenticate@AuthController');
//$r->delete('/auth', 'logout@AuthController');

// example routes - comment them out and use as reference
//$r->addHandler(function (ServerRequest $request, RequestHandlerInterface $next) {
//    return $next->handle($request)
//        ->withHeader('X-Handeled-By', 'closure middleware');
//});
//
//$r->addGroup('/foo', function (MiddlewareRouteCollector $router) {
//    $router->addHandler(function (ServerRequest $request, RequestHandlerInterface $next) {
//        return $next->handle($request)
//            ->withHeader('X-foo', 'another closure handler');
//    });
//
//    $router->get('/bar', function (ServerRequest $request) {
//        /** @var ServerResponse $response */
//        $response = Application::app()->make(ServerResponse::class);
//        return $response->withBody(stream_for('<h1>Bazinga!</h1>'));
//    });
//});
//
//$r->get('/error', 'unexpectedError@ErrorController');

<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

$kernel = new App\Kernel();
$twig = $kernel->getTemplateEngine();
$database = $kernel->getDatabase();

$request = Request::createFromGlobals();

$routes = new RouteCollection();
$routes->add('app_index', new Route('/', [
    '_controller' => App\Controller\IndexController::class,
    '_method' => 'index'
]));
$routes->add('app_add', new Route('/add', [
    '_controller' => App\Controller\IndexController::class,
    '_method' => 'add'
]));
$routes->add('app_run', new Route('/run', [
    '_controller' => App\Controller\IndexController::class,
    '_method' => 'run'
]));
$routes->add('app_result', new Route('/result', [
    '_controller' => App\Controller\IndexController::class,
    '_method' => 'result'
]));

$routes->add('app_show', new Route('/res/{id}/show', [
    '_controller' => App\Controller\IndexController::class,
    '_method' => 'show'
]));

$routes->add('app_edit', new Route('/res/{id}/edit', [
    '_controller' => App\Controller\IndexController::class,
    '_method' => 'edit'
]));

$context = new RequestContext();
$matcher = new UrlMatcher($routes, $context);

try {
    $attributes = $matcher->match($request->getPathInfo());
    //$request->attributes->add($attributes);
    $controller = new $attributes['_controller']($twig, $database);

    if (!method_exists($controller, $attributes['_method'])) {
        throw new ResourceNotFoundException(sprintf('Method %s not found', $attributes['_method']));
    }

    $response = call_user_func_array([$controller, $attributes['_method']], [$request]);
} catch (ResourceNotFoundException $exception) {
    $response = new Response($exception->getMessage(), Response::HTTP_NOT_FOUND);
} catch (\Exception $exception) {
    $response = new Response($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
}

$response->send();

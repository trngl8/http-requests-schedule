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

$request = Request::createFromGlobals();

$routes = new RouteCollection();
$routes->add('index', new Route('/', ['_controller' => 'App\Controller\IndexController::index']));
$routes->add('add', new Route('/add', ['_controller' => 'App\Controller\IndexController::add']));
$routes->add('run', new Route('/run', ['_controller' => 'App\Controller\IndexController::run']));
$routes->add('result', new Route('/result', ['_controller' => 'App\Controller\IndexController::result']));

$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

$attributes = $matcher->match($request->getPathInfo());

try {
    $request->attributes->add($attributes);
    $parts = explode('::', $attributes['_controller']);
    $controller = [new $parts[0]($twig, $request), $parts[1]];

    $arguments = [$request, $twig];

    $response = call_user_func_array($controller, $arguments);
} catch (ResourceNotFoundException $exception) {
    $response = new Response('Not Found', 404);
} catch (\Exception $exception) {
    $response = new Response('An error occurred', 500);
}

$response->send();

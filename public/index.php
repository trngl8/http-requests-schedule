<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$projectDir = __DIR__.'/../';
$dotenv = new Dotenv();
$dotenv->load($projectDir . '.env');

$request = Request::createFromGlobals();

$response = new Response('Not Found', 404);

if ($request->getPathInfo() === '/' ) {
    $controller = new App\BaseController($request);
    $response = call_user_func_array([$controller, 'index'], [['title' => 'Hello, my schedule']]);
}

if ($request->getMethod() === 'GET' && $request->getPathInfo() === '/add') {
    $controller = new App\BaseController($request);
    $response = call_user_func_array([$controller, 'add'], [$request]);
}

if ($request->getMethod() === 'POST' && $request->getPathInfo() === '/add') {
    $controller = new App\BaseController($request);
    $response = call_user_func_array([$controller, 'add'], [$request]);
}

if ($request->getMethod() === 'POST' && $request->getPathInfo() === '/run') {
    $controller = new App\BaseController($request);
    $response = call_user_func_array([$controller, 'run'], [$request]);
}

if ($request->getMethod() === 'GET' && $request->getPathInfo() === '/result') {
    $controller = new App\BaseController($request);
    $response = call_user_func_array([$controller, 'result'], [$request]);
}

$response->send();

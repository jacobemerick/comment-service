<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Aura\Router\RouterFactory;

$router_factory = new RouterFactory;
$router = $router_factory->newInstance();

$router->attach('comment', '/comment', function($router) {

    $router->addPost('create', '/');
    $router->addGet('view', '/{id}');
    $router->addPatch('update', '/{id}');
    $router->addPut('replace', '/{id}');
    $router->addDelete('delete', '/{id}');

    $router->addGet('view-recent', '/recent');
    $router->addGet('view-thread', '/thread');

});

$router->attach('commenter', '/commenter', function($router) {

    $router->addPost('create', '/');
    $router->addGet('view', '/{id}');
    $router->addPatch('update', '/{id}');

});

$path = parse_url($_SERVER['REQUEST_URL'], PHP_URL_PATH);
$route = $router->match($path, $_SERVER);


use Aura\Dispatcher\Dispatcher;

$dispatcher = new Dispatcher;

$dispatcher->setMethodParam('action');

$dispatcher->setObject('comment', new Comment);
$dispatcher->setObject('commenter', new Commenter);

$dispatcher->__invoke($route);

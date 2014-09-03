<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Aura\Router\RouterFactory;
use Aura\Dispatcher\Dispatcher;
use Aura\Sql\ExtendedPdo;

use Jacobemerick\CommentService\CommentFactory;
use Jacobemerick\CommentService\CommenterFactory;

// define the routes
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

// parse path and pass to router
$path = parse_url($_SERVER['REQUEST_URL'], PHP_URL_PATH);
$route = $router->match($path, $_SERVER);

// define the database connection
$extendedPdo = new ExtendedPdo(
    'mysql:host=localhost;dbname=test',
    'username',
    'password'
);

// dispatch based on the core routes
$dispatcher = new Dispatcher;

$dispatcher->setObjectParam('object');
$dispatcher->setMethodParam('action');

$dispatcher->setObject('comment', new CommentFactory($extendedPdo));
$dispatcher->setObject('commenter', new CommenterFactory($extendedPdo));

$dispatcher->__invoke($route);


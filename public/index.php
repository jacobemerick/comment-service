<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Aura\Router\RouterFactory;
use Aura\Dispatcher\Dispatcher;
use Aura\Sql\ExtendedPdo;

use Jacobemerick\CommentService\Comment;
use Jacobemerick\CommentService\Commenter;

// define the routes
$router_factory = new RouterFactory;
$router = $router_factory->newInstance();

$router->attach('comment', '/comment', function($router) {

    $router->addGet('create', '');

    $router->addGet('view', '/{id}')
        ->addTokens([
            'id' => '\d+',
        ])
        ->addAccept([
            'application/json',
        ]);

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
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = $router->match($path, $_SERVER);

// define the database connection
$extendedPdo = new ExtendedPdo(
    'mysql:host=localhost;dbname=test',
    'username',
    'password'
);

// define the main objects for the service
$comment = new Comment($extendedPdo);
$commenter = new Commenter($extendedPdo);

// dispatch based on the core routes
$dispatcher = new Dispatcher;
$dispatcher->setObjectParam('action');

$dispatcher->setObject('comment.create', function() use ($comment, $commenter) {
    return $comment->create($_POST, $commenter);
});

$dispatcher->setObject('comment.view', function($id) use ($comment) {
    return $comment->read($id);
});

$result = $dispatcher->__invoke($route->params);
var_dump($result);


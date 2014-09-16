<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Aura\Dispatcher\Dispatcher;
use Aura\Filter\FilterFactory;
use Aura\Router\RouterFactory;
use Aura\Sql\ExtendedPdo;
use Aura\Web\WebFactory;

use Jacobemerick\CommentService\Domain\Comment;
use Jacobemerick\CommentService\Domain\Commenter;
use Jacobemerick\CommentService\Web\Action;
use Jacobemerick\CommentService\Web\Response;

// define the routes
$router_factory = new RouterFactory;
$router = $router_factory->newInstance();

$router->attach('comment', '/comment', function($router) {

    $router->addPost('create', '');

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

// create request and response objects
$web_factory = new WebFactory($GLOBALS);
$request = $web_factory->newRequest();
$response = $web_factory->newResponse();

// parse path and pass to router
$path = parse_url($request->url->get(), PHP_URL_PATH);
$route = $router->match($path, $request->server);

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

$filterFactory = new FilterFactory();

$dispatcher->setObject('comment.create', function() use (
    $request,
    $comment,
    $commenter,
    $filterFactory
) {
    $action = new Action\CommentCreateAction(
        $request,
        $comment,
        $commenter,
        $filterFactory,
        new Responder\CommentCreateResponder()
    );
});

$dispatcher->setObject('comment.view', function($id) use ($comment) {
    return $comment->read($id);
});

$result = $dispatcher->__invoke($route->params);
var_dump($result);


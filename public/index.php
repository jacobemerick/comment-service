<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Aura\Dispatcher\Dispatcher;
use Aura\Filter\FilterFactory;
use Aura\Router\RouterFactory;
use Aura\Sql\ExtendedPdo;

use Jacobemerick\CommentService\Comment;
use Jacobemerick\CommentService\Commenter;

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

$filterFactory = new FilterFactory();

$dispatcher->setObject('comment.create', function() use ($comment, $commenter, $filterFactory) {
    $filter = $filterFactory->newInstance();
    $error_list = [];

    if (empty($_POST['commenter'])) {
        array_push($error_list, 'Missing a commenter set of params.');
    } else {
        $filter = $filterFactory->newInstance();

        $filter->addHardRule('name', $filter::IS_NOT, 'blank');
        $filter->addSoftRule('name', $filter::IS, 'string');
        $filter->addSoftRule('name', $filter::IS, 'strlenMax', 100);
        $filter->useFieldMessage('name', 'Name is a required field and cannot be longer than 100 chars.');

        $filter->addHardRule('email', $filter::IS_NOT, 'blank');
        $filter->addSoftRule('email', $filter::IS, 'email');
        $filter->addSoftRule('email', $filter::IS, 'strlenMax', 100);
        $filter->useFieldMessage('email', 'Email is a required field and cannot be longer than 100 chars.');

        if (!empty($_POST['commenter']['url'])) {
            $filter->addSoftRule('url', $filter::IS, 'url');
            $filter->addSoftRule('url', $filter::IS, 'strlenMax', 100);
            $filter->useFieldMessage('url', 'URL must be a valid URL and cannot be longer than 100 chars.');
        }

        if (!empty($_POST['commenter']['key'])) {
            $filter->addSoftRule('key', $filter::IS, 'alnum');
            $filter->addSoftRule('key', $filter::IS, 'strlen', 10);
            $filter->useFieldMessage('key', 'Commenter key was not recognized.');
        }

        $success = $filter->values($_POST['commenter']);
        if (!$success) {
            $errors = $filter->getMessages();
            foreach ($errors as $key_list) {
                foreach ($key_list as $error_message) {
                    array_push($error_list, $error_message);
                }
            }
        }
    }

    $filter = $filterFactory->newInstance();

    $filter->addHardRule('body', $filter::IS_NOT, 'blank');
    $filter->addSoftRule('body', $filter::IS, 'string');
    $filter->useFieldMessage('body', 'Comment must have a body attached');

    $success = $filter->values($_POST);
    if (!$success) {
        $errors = $filter->getMessages();
        foreach ($errors as $key_list) {
            foreach ($key_list as $error_message) {
                array_push($error_list, $error_message);
            }
        }
    }

    return $comment->create($_POST, $commenter);
});

$dispatcher->setObject('comment.view', function($id) use ($comment) {
    return $comment->read($id);
});

$result = $dispatcher->__invoke($route->params);
var_dump($result);


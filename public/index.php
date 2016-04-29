<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Aura\Di\ContainerBuilder;
use Jacobemerick\Talus\Talus;

$builder = new ContainerBuilder();
$di = $builder->newInstance();
$swagger = fopen('../swagger.json', 'r');

$talus = new Talus([
    'container' => $di,
    'swagger' => $swagger,
]);

// todo add middleware as needed
$talus->addMiddleware(function ($req, $res, $next) {
    if (!$req->getBody()->isReadable()) {
        return;
    }

    $body = (string) $req->getBody();
    if (empty($body)) {
        return;
    }

    $body = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return;
    }

    $req = $req->withParsedBody($body);
    $next($req, $res);
});

// todo add error handler

$talus->run();

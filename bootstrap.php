<?php

require_once __DIR__ . '/vendor/autoload.php';

use Aura\Di\ContainerBuilder;
use Aura\Sql\ExtendedPdo;
use Jacobemerick\Talus\Talus;

// load the config for the application
$config_path = __DIR__ . '/config.json';

$handle = @fopen($config_path, 'r');
if ($handle === false) {
    throw new RuntimeException("Could not load config");
}
$config = fread($handle, filesize($config_path));
fclose($handle);

$config = json_decode($config);
$last_json_error = json_last_error();
if ($last_json_error !== JSON_ERROR_NONE) {
    throw new RuntimeException("Could not parse config - JSON error detected");
}

$builder = new ContainerBuilder();
$di = $builder->newInstance();

$di->params['Aura\Sql\ExtendedPdo'] = (array) $config->database;
$di->set('dbal', $di->lazyNew('Aura\Sql\ExtendedPdo'));

$swagger = fopen(__DIR__ . '/swagger.json', 'r');

$talus = new Talus([
    'container' => $di,
    'swagger' => $swagger,
]);

// todo add middleware as needed

// todo add check so this only toggles when needed
$talus->addMiddleware(function ($req, $res, $next) {
    if (!$req->getBody()->isReadable()) {
        return;
    }

    $body = (string) $req->getBody();
    if (empty($body)) {
        return $next($req, $res);
    }

    $body = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return;
    }

    $req = $req->withParsedBody($body);
    return $next($req, $res);
});

// todo add error handler

$talus->run();

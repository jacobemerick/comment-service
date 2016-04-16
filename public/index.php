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
$talus->run();

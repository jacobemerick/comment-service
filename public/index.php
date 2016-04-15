<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Jacobemerick\Talus\Talus;

$swagger = fopen('../swagger.json', 'r');
$talus = new Talus([
    'swagger' => $swagger,
]);
$talus->run();

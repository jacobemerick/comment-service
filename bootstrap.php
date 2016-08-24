<?php

date_default_timezone_set('America/Phoenix');

$startTime = microtime(true);
$startMemory = memory_get_usage();

require_once __DIR__ . '/vendor/autoload.php';

use Aura\Di\ContainerBuilder;
use Aura\Sql\ExtendedPdo;
use AvalancheDevelopment\Talus\Talus;

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
$di = $builder->newInstance($builder::AUTO_RESOLVE);

// set up db and models
$di->set('dbal', $di->lazyNew(
    'Aura\Sql\ExtendedPdo',
    (array) $config->database
));
$di->types['Aura\Sql\ExtendedPdo'] = $di->lazyGet('dbal');

$di->set('commentModel', $di->lazyNew('Jacobemerick\CommentService\Model\Comment'));
$di->set('commentBodyModel', $di->lazyNew('Jacobemerick\CommentService\Model\CommentBody'));
$di->set('commentDomainModel', $di->lazyNew('Jacobemerick\CommentService\Model\CommentDomain'));
$di->set('commentLocationModel', $di->lazyNew('Jacobemerick\CommentService\Model\CommentLocation'));
$di->set('commentPathModel', $di->lazyNew('Jacobemerick\CommentService\Model\CommentPath'));
$di->set('commentRequestModel', $di->lazyNew('Jacobemerick\CommentService\Model\CommentRequest'));
$di->set('commentThreadModel', $di->lazyNew('Jacobemerick\CommentService\Model\CommentThread'));
$di->set('commenterModel', $di->lazyNew('Jacobemerick\CommentService\Model\Commenter'));

// set up serializers
$di->set('commentSerializer', $di->lazyNew('Jacobemerick\CommentService\Serializer\Comment'));
$di->set('commenterSerializer', $di->lazyNew('Jacobemerick\CommentService\Serializer\Commenter'));

// set up notification handler
$di->set('notificationHandler', $di->lazyNew(
    'Jacobemerick\CommentService\Helper\NotificationHandler',
    [
        'mail' => $di->lazyGet('mail'),
        'commenterModel' => $di->lazyGet('commenterModel'),
    ]
));

// set up logger
$di->set('logger', $di->lazyNew(
    'Monolog\Logger',
    [
        'name' => 'default',
    ],
    [
        'pushHandler' => $di->lazyNew(
            'Monolog\Handler\StreamHandler',
            [
                'stream' => __DIR__ . '/logs/default.log',
                'level' => Monolog\Logger::DEBUG,
            ]
        ),
    ]
));

// set up mailer
$di->set('mail', $di->lazyNew(
    'Jacobemerick\Archangel\Archangel',
    [],
    [
        'setLogger' => $di->lazyGet('logger'),
    ]
));

// global time object
$di->set('datetime', new DateTime(
    'now',
    new DateTimeZone('America/Phoenix')
));

// set up swagger
$handle = fopen(__DIR__ . '/swagger.json', 'r');
$swagger = '';
while (!feof($handle)) {
    $swagger .= fread($handle, 8192);
}
fclose($handle);

$swagger = json_decode($swagger);
if (json_last_error() !== JSON_ERROR_NONE) {
    var_dump('oh noes the swagz is badz');
    exit;
}

$talus = new Talus([
    'container' => $di,
    'swagger' => $swagger,
]);

// todo extract middleware to testable classes
$auth = $config->auth;
$talus->addMiddleware(function ($req, $res, $next) use ($auth) {
    if ($req->getUri()->getPath() == '/api-docs') {
        return $next($req, $res);
    }

    $authHeader = base64_encode("{$auth->username}:{$auth->password}");
    $authHeader = "Basic {$authHeader}";

    if ($authHeader != current($req->getHeader('Authorization'))) {
        $res = $res->withStatus(403);
        return $res;
    }

    return $next($req, $res);
});

// todo does this belong in talus?
$talus->addMiddleware(function ($req, $res, $next) {
    $res = $next($req, $res);
    $res = $res->withAddedHeader('Content-Type', 'application/json');
    return $res; 
});

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

$talus->setErrorHandler(function ($req, $res, $e) {
    $body = json_encode([
        'error' => $e->getMessage(),
    ]);

    $res->getBody()->write($body);
    return $res;
});

$talus->run();

$di->get('logger')->addInfo('Runtime stats', [
    'request' => $_SERVER['REQUEST_URI'],
    'time' => (microtime(true) - $startTime),
    'memory' => (memory_get_usage() - $startMemory),
]);

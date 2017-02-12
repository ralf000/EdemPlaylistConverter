<?php

use app\App;
use app\components\logger\FileRoute;
use app\components\logger\Logger;
use Noodlehaus\Config;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$app = new App();

/**
 * init logger
 */
$logger = new Logger();
$logger->routes->attach(new FileRoute([
    'filePath' => 'logs/logs.txt',
    'isEnabled' => true
]));
$app->set($logger);

/**
 * init config
 */
$config = new Config(__DIR__ . '../config');
$app->set($config);
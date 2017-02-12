<?php

use app\components\logger\FileRoute;
use app\components\logger\Logger;

require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * init logger
 */
$logger = new Logger();
$logger->routes->attach(new FileRoute([
    'filePath' => 'logs/logs/txt',
    'isEnabled' => true
]));
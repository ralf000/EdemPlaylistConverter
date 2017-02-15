<?php
/**
 * init strict mode
 */
declare(strict_types = 1);

require __DIR__ . '/autoload.php';

require_once __DIR__ . '/bootstrap/app.php';

try {
    $tvProgram = new \app\classes\TVProgram();
    $tvProgram->check();
} catch (\app\exceptions\FileException $e) {
    echo $e->getException();
} catch (Exception $e) {
    echo $e->getMessage();
}
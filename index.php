<?php
/**
 * init strict mode
 */
declare(strict_types = 1);

require __DIR__ . '/autoload.php';

require_once __DIR__ . '/bootstrap/app.php';

try {
    $playlist = new \app\classes\Playlist(\app\App::get('config')->get('main.inputPlaylist'));
    $playlist->handle();
} catch (\app\exceptions\FileException $e) {
    echo $e->getException();
} catch (Exception $e) {
    echo $e->getMessage();
}
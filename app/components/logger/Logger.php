<?php

namespace app\components\logger;


use app\App;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    /**
     * @var \SplObjectStorage Список роутов
     */
    public $routes;

    /**
     * Logger constructor.
     */
    public function __construct()
    {
        $this->routes = new \SplObjectStorage();
    }


    public function log($level, $message, array $context = array())
    {
        foreach ($this->routes as $route) {
            if (!($route instanceof Route))
                continue;
            if (!$route->isEnabled)
                continue;
            $route->log($level, $message, $context);
        }
    }

    public function successCreatePlaylistLog(int $allChannels, int $remainingChannels)
    {
        $message = 'Всего каналов: ' . $allChannels;
        $message .= ' | ';
        $message .= 'Удалено: ' . ($allChannels - $remainingChannels);
        $message .= ' | ';
        $message .= 'Осталось: ' . $remainingChannels;
        App::get('logger')->info($message);
    }

}
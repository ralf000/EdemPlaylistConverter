<?php

namespace app\components\logger;


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

}
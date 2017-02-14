<?php

namespace app\exceptions;
use app\App;
use app\components\logger\Logger;
use Exception;

/**
 * Class ABaseException
 * @package app\exceptions
 */
abstract class ABaseException extends \Exception
{
    /**
     * @var string Сообщение об ошибке
     */
    protected $message = '';

    public function __construct($message)
    {
        $this->message = $message;
        $context = ['Класс ошибки: ' . static::class, 'Файл: ' . $this->getFile(), 'Строка: ' . $this->getLine()];
        App::get('logger')->error($message, $context);
    }

    /**
     * @return string
     */
    public function getException()
    {
        $output = "<table>{$this->xdebug_message}</table>";
        return $output;
    }
}
<?php

namespace app\exceptions;
use app\components\logger\Logger;

/**
 * Class ABaseException
 * @package app\exceptions
 */
abstract class ABaseException extends \Exception
{
    /**
     * ABaseException constructor.
     */
    public function __construct()
    {
        $context = ['Класс ошибки: ' . static::class, 'Файл: ' . $this->getFile(), 'Строка: ' . $this->getLine()];
        (new Logger())->error($this->getMessage(), $context);
    }

    /**
     * @return string
     */
    public function getException()
    {
        $output = get_class($this) . '<br>';
        $output .= "<table>{$this->xdebug_message}</table>";
        return $output;
    }
}
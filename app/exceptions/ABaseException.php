<?php

namespace app\exceptions;

/**
 * Class ABaseException
 * @package app\exceptions
 */
abstract class ABaseException extends \Exception
{
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
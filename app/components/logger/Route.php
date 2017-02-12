<?php

namespace app\components\logger;


use Psr\Log\AbstractLogger;

abstract class Route extends AbstractLogger
{
    /**
     * @var bool Включен ли роут
     */
    public $isEnabled = false;

    /**
     * @var string Формат даты логов
     */
    private $dateFormat = \DateTime::RFC2822;

    /**
     * Route constructor.
     * @param array $attributes Атрибуты роута
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $attribute => $value) {
            if (property_exists($this, $attribute))
                $this->$attribute = $value;
        }
    }

    /**
     * Текущая дата
     * 
     * @return string
     */
    protected function getDate(){
        return (new \DateTime())->format($this->dateFormat);
    }

    /**
     * Преобразование $context в строку
     *
     * @param array $context
     * @return string
     */
    public function contextStringify(array $context = [])
    {
        return !empty($context) ? json_encode($context) : null;
    }
}
<?php

namespace app;


/**
 * Class App
 * @package app
 */
class App
{
    /**
     * @var null|self
     */
    private static $instance = null;

    /**
     * @var array Глобальные объекты приложения
     */
    private $objects = [];

    /**
     * App constructor.
     */
    private function __construct()
    {
    }

    /**
     * Возвращает объект типа App
     * @return App
     */
    public static function init()
    {
        if (is_null(static::$instance))
            static::$instance = new static;
        return static::$instance;
    }

    /**
     * @param string $objectName
     * @return mixed|null
     */
    public static function get(string $objectName)
    {
        return self::init()->getObject($objectName);
    }

    /**
     * @param string $name
     * @param $object
     * @throws \TypeError
     */
    public static function set(string $name, $object)
    {
        self::init()->setObject($name, $object);
    }

    /**
     * @param string $objectName
     * @return mixed|null
     */
    private function getObject(string $objectName)
    {
        if (!array_key_exists($objectName, $this->objects))
            return null;
        return $this->objects[$objectName];
    }

    /**
     * @param string $objectName
     * @param $object
     * @throws \TypeError
     */
    private function setObject(string $objectName, $object)
    {
        if (!is_object($object))
            throw new \TypeError();
        if (!in_array($objectName, $this->objects))
            $this->objects[$objectName] = $object;
    }
    
}
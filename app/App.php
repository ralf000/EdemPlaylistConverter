<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 12.02.2017
 * Time: 18:46
 */

namespace app;


class App
{
    /**
     * @var \SplObjectStorage Глобальные объекты приложения
     */
    private $objects;

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->objects = new \SplObjectStorage();
    }

    /**
     * @param string $objectName
     * @return object
     */
    public function get(string $objectName) : object
    {
        return $this->objects->$objectName;
    }

    /**
     * @param $object
     * @throws \TypeError
     */
    public function set($object)
    {
        if (!is_object($object))
            throw new \TypeError();
        $this->objects->attach($object);
    }

}
<?php

namespace app\classes;

use app\exceptions\FileException;

/**
 * Class AFile
 * @package app\classes
 */
abstract class AFile
{
    /**
     * @var string
     */
    protected $path = '';
    /**
     * @var bool|null|resource
     */
    protected $descriptor = null;

    /**
     * AFile constructor.
     * @param string $path
     * @throws FileException
     */
    public function __construct($path)
    {
        if (!is_file($path))
            throw new FileException('Неверный путь до файла');

        if (!is_null($this->descriptor))
            return null;
        $this->descriptor = fopen($path, 'r');
        if (!$this->descriptor)
            throw new FileException('Не удалось открыть файл');

        $this->path = $path;
    }

    /**
     * @param \Closure $closure
     */
    protected function handleFile(\Closure $closure)
    {
        while (!feof($this->descriptor)) {
            $line = fgets($this->descriptor);
            $closure($line);
        }
    }

    abstract protected function handleString($line);

    /**
     * @return bool
     */
    public function close() : bool
    {
        return (!$this->descriptor) ? fclose($this->descriptor) : false;
    }

    /**
     * @return bool
     * @throws FileException
     */
    public function delete() : bool
    {
        if (!unlink($this->path))
            throw new FileException('Не могу удалить файл');
        else
            return true;
    }


}
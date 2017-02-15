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
        if (!is_null($this->descriptor))
            return null;
        
        $this->descriptor = fopen($path, 'r');
        if (!$this->descriptor)
            throw new FileException('Не удалось открыть файл');

        $this->path = $path;
    }

    /**
     * @param $descriptor
     * @return bool
     */
    protected function close($descriptor) : bool
    {
        return fclose($descriptor);
    }

    /**
     * @param $path
     * @return bool
     * @throws FileException
     */
    protected function delete($path) : bool
    {
        if (!unlink($path))
            throw new FileException('Не могу удалить файл');
        else
            return true;
    }


}
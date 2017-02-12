<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 12.02.2017
 * Time: 16:52
 */

namespace app\components\logger;


class FileRoute extends Route
{
    /**
     * @var string Путь к файлу
     */
    private $filePath = '';

    /**
     * @var string Шаблон сообщения
     */
    private $template = '{date} {level} {message} {context}';

    /**
     * FileRoute constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (!file_exists($this->filePath))
            touch($this->filePath);
    }


    public function log($level, $message, array $context = array())
    {
        $message = $this->initMessage($level, $message, $context);
        file_put_contents($this->filePath, $message . PHP_EOL, FILE_APPEND);
    }

    private function initMessage($level, $message, $context)
    {
        return trim(strtr($this->template, [
            '{date}' => $this->getDate(),
            '{level}' => $level,
            '{message}' => $message,
            '{context}' => $this->contextStringify($context)
        ]));
    }

}
<?php

namespace app\classes;


class Channel
{
    /**
     * @var array [header => 'header', url => 'url']
     */
    private $channel = [];

    /**
     * @var string
     */
    private $title = '';
    /**
     * @var string
     */
    private $group = '';

    /**
     * Channel constructor.
     * @param array $channel [title => 'title', url => 'url']
     */
    public function __construct(array $channel)
    {
        $this->$channel = $channel;
        $this->parse();
    }

    /**
     * Парсит канал
     *
     * @return array
     */
    private function parse() : array
    {
        preg_match('~group\-title="(.*)"\s*\,\s*(\w+)~iUu', $this->channel['header'], $result);
        if (count($result) !== 3)
            return false;
        
        array_shift($result);
        $this->group = $this->clean($result[0]);
        $this->title = $this->clean($result[1]);
        
        return true;
    }

    /**
     * Очищает строку
     *
     * @param string $string
     * @return string
     */
    private function clean(string $string) : string
    {
        return trim(strtolower($string));
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }


}
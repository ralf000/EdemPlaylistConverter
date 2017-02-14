<?php

namespace app\classes;


use app\App;
use Noodlehaus\Config;

class Playlist extends AFile
{
    /**
     * @var Channel
     */
    private $channel;

    /**
     * @var array массив каналов типа Channel
     */
    private $channels = [];

    /**
     * @var Config
     */
    private $config;

    /**
     * Playlist constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct($path);
        $this->config = App::get('config');
    }


    public function handle()
    {
        while (!feof($this->descriptor)) {
            $line = trim(fgets($this->descriptor));

            if (empty($line) || $line == '#EXTM3U')
                continue;

            //example: #EXTINF:0,РБК-ТВ
            if (mb_substr($line, 0, 7) == '#EXTINF') {
                $channelData = [];
                list(, $channelData['title']) = explode(',', $line);
            } else if (mb_substr($line, 0, 7) == '#EXTGRP') {
                //example: #EXTGRP:новости
                list(, $channelData['group']) = explode(':', $line);
            } else {
                $channelData['url'] = $line;
                $this->channel = new Channel($channelData);
                $this->changeChannelAttribute();
                if ($this->filterChannel()) {
                    $this->channels[] = $this->channel;
                }
            }
        }
        $this->close();
        $this->sort(SORT_ASC);
        $this->createPlaylist();
    }

    private function createPlaylist()
    {
        $playlistName = $this->config->get('main.outputFileName');
        $playlistPath = __DIR__ . '/../../' . $playlistName;
        $descriptor = fopen($playlistPath, 'w');
        fwrite($descriptor, '#EXTM3U' . PHP_EOL);
        foreach ($this->channels as $channel) {
            /**
             * @var Channel $channel
             */
            fwrite($descriptor, $channel->convert());
        }
    }

    private function changeChannelAttribute()
    {
        $title = $this->channel->getTitle();

        $renameChannels = $this->handleConfig('renameChannels');
        $renameChannels = $this->arrayKeysToLowerCase($renameChannels);
        if (array_key_exists($title, $renameChannels))
            $this->channel->setTitle($renameChannels[$title]);

        $changeGroups = $this->handleConfig('changeGroups');
        $changeGroups = $this->arrayKeysToLowerCase($changeGroups);
        if (array_key_exists($title, $changeGroups))
            $this->channel->setGroup($changeGroups[$title]);
    }

    private function filterChannel() : bool
    {
        $excludeChannels = $this->handleConfig('excludeChannels');
        if (in_array($this->channel->getTitle(), $excludeChannels))
            return false;

        $excludeGroups = $this->handleConfig('excludeGroups');
        if (in_array($this->channel->getGroup(), $excludeGroups))
            return false;

        return true;
    }

    private function sort($sortDirection)
    {
        return usort($this->channels, function ($a, $b) use ($sortDirection) {
            /**
             * @var Channel $a
             * @var Channel $b
             */
            if ($sortDirection === SORT_ASC)
                return $a->getTitle() <=> $b->getTitle();
            else
                return $b->getTitle() <=> $a->getTitle();
        });
    }

    private function handleConfig($arrayName)
    {
        return array_map('mb_strtolower', $this->config->get($arrayName));
    }

    private function arrayKeysToLowerCase(array $array) : array
    {
        $output = [];
        foreach ($array as $key => $item) {
            $output[mb_strtolower($key)] = $item;
        }
        return $output;
    }

}
<?php

namespace app\classes;


use app\App;
use app\components\helpers\ArrayHelper;
use app\components\helpers\MbString;
use app\components\logger\Logger;
use Noodlehaus\Config;

class Playlist extends AFile implements ICreatable
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
     * @var int Общее количество каналов
     */
    private $channelCounter = 0;

    /**
     * Playlist constructor.
     */
    public function __construct()
    {
        $path = App::get('config')->get('main.inputPlaylist');
        parent::__construct($path);
        $this->config = App::get('config');
    }


    public function create()
    {
        while (!feof($this->descriptor)) {

            $line = MbString::mb_trim(fgets($this->descriptor));

            if (empty($line) || $line == '#EXTM3U')
                continue;

            if (mb_substr($line, 0, 7) == '#EXTINF') {
                $channelData = [];
                //example: #EXTINF:0,РБК-ТВ
                list(, $channelData['title']) = explode(',', $line);

                $this->channelCounter++;
            } else if (mb_substr($line, 0, 7) == '#EXTGRP') {
                //example: #EXTGRP:новости
                list(, $channelData['group']) = explode(':', $line);
            } else if (mb_substr($line, 0, 4) == 'http') {
                $channelData['url'] = $line;
                $this->channel = new Channel($channelData);
                $this->changeChannelAttribute();
                if ($this->filterChannel()) {
                    $this->channels[] = $this->channel;
                }
            }
        }
        $this->close($this->descriptor);

        $this->addAdditionalChannels();
        $this->sort(SORT_ASC);
        $this->createPlaylist();
    }

    private function createPlaylist()
    {
        $playlistName = $this->config->get('main.outputPlaylistName');
        $playlistPath = __DIR__ . '/../../' . $playlistName;
        $descriptor = fopen($playlistPath, 'w');
        fwrite($descriptor, '#EXTM3U' . PHP_EOL);
        foreach ($this->channels as $channel) {
            /**
             * @var Channel $channel
             */
            fwrite($descriptor, $channel->convert());
        }
        $this->close($descriptor);
        App::get('logger')->successCreatePlaylistLog($this->channelCounter, count($this->channels));
    }

    private function changeChannelAttribute()
    {
        $title = $this->channel->getTitle();

        $renameChannels = $this->config->get('renameChannels');
        if (array_key_exists($title, $renameChannels))
            $this->channel->setTitle($renameChannels[$title]);

        $changeGroups = ArrayHelper::arrayValuesChangeCase($this->config->get('changeGroups'));
        if (array_key_exists($title, $changeGroups))
            $this->channel->setGroup($changeGroups[$title]);
    }

    private function filterChannel() : bool
    {
        $excludeChannels = $this->config->get('excludeChannels');
        if (in_array($this->channel->getTitle(), $excludeChannels))
            return false;

        $excludeGroups = ArrayHelper::arrayValuesChangeCase($this->config->get('excludeGroups'));
        if (in_array($this->channel->getGroup(), $excludeGroups))
            return false;

        return true;
    }

    private function addAdditionalChannels()
    {
        $additionalChannels = $this->config->get('additionalChannels');
        foreach ($additionalChannels as $additionalChannel) {
            if (!isset($additionalChannel['group']) || empty($additionalChannel['group']))
                $additionalChannel['group'] = 'другое';
            $this->channels[] = new Channel($additionalChannel);
        }
    }

    private
    function sort($sortDirection)
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

    /**
     * @return array
     */
    public
    function getChannels() : array
    {
        return $this->channels;
    }


}
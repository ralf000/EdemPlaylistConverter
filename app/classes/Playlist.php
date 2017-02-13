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


    public function handleFile()
    {
        while (!feof($this->descriptor)) {
            $line = fgets($this->descriptor);
            if (empty($line) || $line == '#EXTM3U')
                continue;

            if (mb_substr($line, 0, 7) == '#EXTINF') {
                $channelData = [];
                $channelData['header'] = $line;
            } else {
                $channelData['url'] = $line;
                $this->channel = new Channel($channelData);
                if ($this->filter()) {
                    $this->handle();
                    // TODO записать объект channel в новый плейлист
                }
            }
        }
    }

    private function handle()
    {
        $title = $this->channel->getTitle();

        $changeGroups = $this->handleConfig('changeGroups');
        if (array_key_exists($title, $changeGroups))
            $this->channel->setGroup($changeGroups[$title]);

        $renameChannels = $this->handleConfig('renameChannels');
        if (array_key_exists($title, $renameChannels))
            $this->channel->setTitle($renameChannels[$title]);
    }

    private function filter() : bool
    {
        $excludeGroups = $this->handleConfig('excludeGroups');
        if (in_array($this->channel->getGroup(), $excludeGroups))
            return false;
        $excludeChannels = $this->handleConfig('excludeChannels');
        if (in_array($this->channel->getTitle(), $excludeChannels))
            return false;

        return true;
    }

    private function handleConfig($arrayName)
    {
        $config = array_map('strtolower', $this->config->get($arrayName));
        return array_change_key_case($config, CASE_LOWER);
    }

}
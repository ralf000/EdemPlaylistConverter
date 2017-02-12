<?php

namespace app\classes;


class Playlist extends AFile
{
    private $channels = [];

    public function setChannels()
    {
        $this->handleFile($this->handleString);
    }

    protected function handleString($line)
    {
        if (empty($line) || $line == '#EXTM3U')
            continue;

        if (mb_substr($line, 0, 7) == '#EXTINF') {
            // TODO
        } else {
            // TODO
        }
    }

    private function checkChannel($line)
    {
        // TODO
    }


}
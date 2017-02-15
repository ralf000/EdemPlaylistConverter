<?php

namespace app\classes;


use app\App;
use app\components\helpers\MbString;
use app\exceptions\FileException;
use Noodlehaus\Config;

/**
 * Class TVProgram
 */
class TVProgram extends AFile implements ICreatable
{
    /**
     * @var string
     */
    private $outputTVName = '';

    /**
     * @var string
     */
    private $outputTVPath = '';

    /**
     * TVProgram constructor.
     */
    public function __construct()
    {
        $this->path = App::get('config')->get('main.inputTVProgram');
        $this->outputTVName = App::get('config')->get('main.outputTVProgramName');
        $this->outputTVPath = __DIR__ . '/../../../' . $this->outputTVName;
        parent::__construct($this->path);
    }

    /**
     * Скачивает телепрограмму на сервер
     */
    public function create()
    {
        $InputTVGzData = file_get_contents($this->path);
        if (!$InputTVGzData || !$this->checkCorrectlyDate())
            $InputTVGzData = file_get_contents($this->getReserveTvProgramPath());
        $outputTVGzPath = $this->outputTVPath . '.gz';
        file_put_contents($outputTVGzPath, $InputTVGzData);
    }

    /**
     * Проверяет наличие телепрограммы для каналов плейлиста
     * @throws FileException
     */
    public function check()
    {
        $xml = $this->getXml($this->outputTVPath);
        $xmlChannels = [];
        foreach ($xml as $item) {
            /**
             * @var \SimpleXMLElement $item
             */
            $xmlChannels[] = MbString::mb_trim((string)$item->{'display-name'});
        }

        $playlistChannels = $this->getPlaylistChannels();
        $withoutProgram = [];
        foreach ($playlistChannels as $playlistChannel) {
            /**
             * @var Channel $playlistChannel
             */
            $playlistChannelTitle = $playlistChannel->getTitle();
            if (!in_array($playlistChannelTitle, $xmlChannels))
                $withoutProgram[] = $playlistChannelTitle;
        }
        $this->delete($this->outputTVPath);
        echo $this->showChannelsWithoutProgram($withoutProgram);
    }

    /**
     * @param string $xmlTvPath
     * @return \SimpleXMLElement
     * @throws FileException
     */
    private function getXml(string $xmlTvPath) : \SimpleXMLElement
    {
        $this->gzUnzip();
        $xml = simplexml_load_file($xmlTvPath);
        if (!$xml)
            throw new FileException('Не удалось открыть ' . $this->outputTVName);
        return $xml;
    }

    /**
     * @return bool
     * @throws FileException
     */
    private function checkCorrectlyDate() : bool
    {
        $dates = [];
        $xml = $this->getXml($this->outputTVPath);
        foreach ($xml as $item) {
            /**
             * @var \SimpleXMLElement $item
             */
            if (!$item->{'title'})
                continue;
            $date[] = mb_substr($item->attributes()->start, 0, 8);
        }
        $minDate = new \DateTime(min($dates));
        $maxDate = new \DateTime(max($dates));
        $now = new \DateTime();
        if (($now < $maxDate) && ($now > $minDate))
            return true;
        return false;
    }

    /**
     * @return string
     */
    private function getReserveTvProgramPath() : string
    {
        return $this->path = App::get('config')->get('main.inputReserveTVProgram');
    }

    /**
     * Отображает каналы без телепрограммы
     * @param array $withoutProgram
     * @return string
     */
    private
    function showChannelsWithoutProgram(array $withoutProgram) : string
    {
        if (empty($withoutProgram)) {
            $output = '<h3>Для всех телеканалов текущего плейлиста доступна телепрограмма</h3>';
        } else {
            $output = '<h3>Телепрограмма не найдена для следующих телеканалов:</h3>';
            $output .= '<ul>';
            foreach ($withoutProgram as $channel) {
                $output .= '<li>' . htmlspecialchars($channel) . '</li>';
            }
            $output .= '</ul>';
        }
        return $output;
    }

    /**
     * @throws FileException
     */
    private
    function gzUnzip()
    {
        $tvInput = gzopen($this->path, 'r');
        $tvOutput = fopen($this->outputTVPath, 'w');
        if (!$tvInput || !$tvOutput)
            throw new FileException('Не удалось открыть один или несколько файлов телепрограммы');

        while (($line = fgets($tvInput)) !== FALSE) {
            fwrite($tvOutput, $line);
        }
        $this->close($tvInput);
        $this->close($tvOutput);
    }

    /**
     * @return array
     */
    private
    function getPlaylistChannels() : array
    {
        $playlist = new Playlist();
        $playlist->create();
        return $playlist->getChannels();
    }

}
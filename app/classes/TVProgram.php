<?php

namespace app\classes;


use app\App;
use app\components\helpers\MbString;
use app\exceptions\FileException;
use Noodlehaus\Config;

/**
 * Class TVProgram
 */
class TVProgram extends AFile implements ICreating
{
    /**
     * @var string xmlTv.xml.gz
     */
    private $outputTVName = '';

    /**
     * @var string path/xmlTv.xml
     */
    private $outputTVPath = '';

    /**
     * @var string path/xmlTv.xml.gz
     */
    private $outputTVGzPath = '';

    /**
     * TVProgram constructor.
     */
    public function __construct()
    {
        $this->path = App::get('config')->get('main.inputTVProgram');
        parent::__construct($this->path);
        $this->outputTVName = App::get('config')->get('main.outputTVProgramName');
        $this->outputTVPath = __DIR__ . '/../../' . $this->outputTVName;
        $this->outputTVGzPath = $this->outputTVPath . '.gz';
    }

    /**
     * Скачивает телепрограмму на сервер
     */
    public function create()
    {
        $inputTVGzData = file_get_contents($this->path);
        if (!$this->save($inputTVGzData) || !$this->checkCorrectlyDate()) {
            $inputTVGzData = file_get_contents($this->getReserveTvProgramPath());
            if (!$this->save($inputTVGzData))
                throw new FileException('Не удалось сохранить файл телепрограммы');
        }
        $this->delete($this->outputTVPath);
    }

    private function save(string $data = '')
    {
        return ($data) ? file_put_contents($this->outputTVGzPath, $data) : false;
    }

    /**
     * Проверяет наличие телепрограммы для каналов плейлиста
     * @throws FileException
     */
    public function check()
    {
        $this->create();
        $xml = $this->getSimpleXml();
        if (!$xml)
            throw new FileException('Не удалось открыть файл ' . $this->outputTVPath);

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
     * @return \SimpleXMLElement
     * @throws FileException
     */
    private function getSimpleXml() : \SimpleXMLElement
    {
        $xmlTv = $this->gzUnzip();
        $simpleXml = simplexml_load_file($this->outputTVPath);
        return $simpleXml;
    }

    /**
     * @return bool
     * @throws FileException
     */
    private function checkCorrectlyDate() : bool
    {
        $dates = [];
        $xml = $this->getSimpleXml();
        if (!$xml)
            return false;
        foreach ($xml as $item) {
            /**
             * @var \SimpleXMLElement $item
             */
            if (!$item->{'title'})
                continue;
            $dates[] = mb_substr($item->attributes()->start, 0, 8);
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
    private function showChannelsWithoutProgram(array $withoutProgram) : string
    {
        if (empty($withoutProgram)) {
            $output = '<h3>Телепрограмма доступна для всех телеканалов текущего плейлиста</h3>';
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
    private function gzUnzip()
    {
        $tvInput = gzopen($this->outputTVGzPath, 'r');
        $tvOutput = fopen($this->outputTVPath, 'w');
        if (!$tvInput || !$tvOutput)
            throw new FileException('Не удалось открыть один или несколько файлов телепрограммы');
        while (($line = fgets($tvInput)) !== FALSE) {
            fwrite($tvOutput, $line);
        }
        $this->close($tvInput);
        $this->close($tvOutput);
        return true;
    }

    /**
     * @return array
     */
    private function getPlaylistChannels() : array
    {
        $playlist = new Playlist();
        $playlist->create();
        return $playlist->getChannels();
    }

}
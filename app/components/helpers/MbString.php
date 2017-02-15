<?php

namespace app\components\helpers;

/**
 * Class MbString
 */
class MbString
{
    /**
     * @param string $string
     * @return string
     */
    public static function mb_ucwords(string $string) : string
    {
        $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
        return ($string);
    }

    /**
     * @param string $string
     * @param string $enc
     * @return string
     */
    public static function mb_ucfirst(string $string, string $enc = 'UTF-8') : string
    {
        return mb_strtoupper(mb_substr($string, 0, 1, $enc), $enc) .
        mb_substr($string, 1, mb_strlen($string, $enc), $enc);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function mb_trim(string $string) : string
    {
        $string = preg_replace("/(^\s+)|(\s+$)/us", "", $string);
        return $string;
    }
}
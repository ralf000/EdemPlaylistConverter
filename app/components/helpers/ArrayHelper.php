<?php

namespace app\components\helpers;


class ArrayHelper
{
    /**
     * @param array $array
     * @param $case
     * @return array
     */
    public static function arrayValuesChangeCase(array $array, $case = CASE_LOWER) : array
    {
        $function = ($case === CASE_LOWER) ? 'mb_strtolower' : 'mb_strtoupper';
        return array_map($function, $array);
    }

    /**
     * @param array $array
     * @param $case
     * @return array
     */
    public static function arrayKeysChangeCase(array $array, $case = CASE_LOWER) : array
    {
        $output = [];
        foreach ($array as $key => $item) {
            $key = ($case === CASE_LOWER) ? mb_strtolower($key) : mb_strtoupper($key);
            $output[mb_strtolower($key)] = $item;
        }
        return $output;
    }
}
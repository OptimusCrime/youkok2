<?php
namespace Youkok\Common\Utilities;

class ArrayHelper
{

    public static function removeFromArray(array $array, $value)
    {
        $newArray = [];
        foreach ($array as $v) {
            if ($v === $value) {
                continue;
            }

            $newArray[] = static::cloneVar($v);
        }
        return $newArray;
    }

    public static function addToArray(array $array, $value)
    {
        $newArray = static::cloneArray($array);
        $newArray[] = $value;

        return $newArray;
    }

    public static function cloneArray(array $array)
    {
        $newArray = [];
        foreach ($array as $v) {
            $newArray[] = static::cloneVar($v);
        }
        return $newArray;
    }

    public static function cloneVar($var)
    {
        if (is_object($var)) {
            return clone $var;
        }

        return $var;
    }

    public static function prependToArray(array $array, $value)
    {
        $newArray = [
            static::cloneVar($value)
        ];

        foreach ($array as $v) {
            $newArray[] = static::cloneVar($v);
        }

        return $newArray;
    }

    public static function limitArray(array $array, $limit)
    {
        if (count($array) <= $limit) {
            return static::cloneArray($array);
        }

        $newArray = [];
        foreach ($array as $k => $v) {
            $newArray[] = $v;
            if (($k + 1) === $limit) {
                break;
            }
        }

        return $newArray;
    }
}

<?php
namespace Youkok\Utilities;

class ArrayHelper {

    public static function removeFromArray(array $array, $value)
    {
        $newArray = [];
        foreach ($array as $v) {
            if ($v === $value) {
                continue;
            }

            $newArray[] = clone $v;
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
            if (is_object($v)) {
                $newArray[] = clone $v;
                continue;
            }

            $newArray = $v;
        }
        return $newArray;
    }
}

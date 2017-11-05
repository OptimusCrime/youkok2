<?php
namespace Youkok\Helpers;

class Utilities
{
    private static $NORWEGIAN_MONTHS = [
        'jan', 'feb', 'mar',
        'apr', 'mai', 'jun',
        'jul', 'aug', 'sep',
        'okt', 'nov', 'des'];

    public static function numberFormat($num)
    {
        return number_format($num, 0, '.', ' ');
    }

    public static function randomToken($length)
    {
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

    public static function prettifySQLDate($d, $excludeTime = true)
    {
        $split1 = explode(' ', $d);
        $split2 = explode('-', $split1[0]);

        $returnString = $split2[2] . '. ' . self::$NORWEGIAN_MONTHS[$split2[1] - 1] . ' ' . $split2[0];

        if ($excludeTime) {
            return $returnString;
        }

        return $returnString . ' @ ' . $split1[1];
    }
}

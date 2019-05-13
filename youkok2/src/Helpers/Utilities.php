<?php
namespace Youkok\Helpers;

const NORWEGIAN_MONTHS = [
    'jan', 'feb', 'mar',
    'apr', 'mai', 'jun',
    'jul', 'aug', 'sep',
    'okt', 'nov', 'des'
];

class Utilities
{
    public static function numberFormat($num): string
    {
        return number_format($num, 0, '.', ' ');
    }

    public static function randomToken($length): string
    {
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

    public static function prettifySQLDate($d, $excludeTime = true): string
    {
        $split1 = explode(' ', $d);
        $split2 = explode('-', $split1[0]);

        $returnString = $split2[2] . '. ' . NORWEGIAN_MONTHS[$split2[1] - 1] . ' ' . $split2[0];

        if ($excludeTime) {
            return $returnString;
        }

        return $returnString . ' @ ' . $split1[1];
    }

    public static function clean($d, $include_time = true): string
    {
        if ($d == 'CURRENT_TIMESTAMP') {
            $d = date('Y-m-d  G:i:s');
        }

        $split1 = explode(' ', $d);
        $split2 = explode('-', $split1[0]);

        return ((int) $split2[2])
            . '. '
            . NORWEGIAN_MONTHS[$split2[1] - 1]
            . ' '
            . $split2[0]
            . ($include_time ? (' @ ' . $split1[1]) : '');
    }

}

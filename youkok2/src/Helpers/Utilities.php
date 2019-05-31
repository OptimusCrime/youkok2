<?php
namespace Youkok\Helpers;

const NORWEGIAN_MONTHS = [
    'jan', 'feb', 'mar',
    'apr', 'mai', 'jun',
    'jul', 'aug', 'sep',
    'okt', 'nov', 'des'
];

const KEYSPACE = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

class Utilities
{
    public static function numberFormat($num): string
    {
        return number_format($num, 0, '.', ' ');
    }

    public static function randomToken($length): string
    {

        $str = '';
        $max = mb_strlen(KEYSPACE, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= KEYSPACE[random_int(0, $max)];
        }
        return $str;
    }

    public static function prettifySQLDate(string $input): string
    {
        $date = explode(' ', $input)[0];
        list($year, $month, $day) = array_map('intval', explode('-', $date));

        $returnString = $day . '. ' . NORWEGIAN_MONTHS[$month - 1] . ' ' . $year;

        return $returnString;
    }

    public static function prettifySQLDateTime(string $input): string
    {
        list($date, $time) = explode(' ', $input);

        $prettyDate = static::prettifySQLDate($date);

        return $prettyDate . ' @ ' . $time;
    }

    public static function clean(string $input): string
    {
        if ($input === 'CURRENT_TIMESTAMP') {
            $input = date('Y-m-d G:i:s');
        }

        return static::prettifySQLDateTime($input);
    }
}

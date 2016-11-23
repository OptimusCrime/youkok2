<?php
declare(strict_types=1);

namespace Youkok\Helpers;

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
}

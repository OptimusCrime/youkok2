<?php
namespace Youkok\Common\Utilities;

class NumberFormatter
{
    public static function format($number): string
    {
        return number_format($number, 0, '', ' ');
    }
}

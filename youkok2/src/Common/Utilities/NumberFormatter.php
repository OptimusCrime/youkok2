<?php
namespace Youkok\Common\Utilities;

class NumberFormatter
{
    public static function format($number)
    {
        return number_format($number, 0, '', ' ');
    }
}

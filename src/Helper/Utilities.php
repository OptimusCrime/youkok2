<?php
declare(strict_types=1);

namespace Youkok\Helper;

class Utilities
{
    public static function numberFormat($num): string
    {
        return number_format($num, 0, '.', ' ');
    }

}

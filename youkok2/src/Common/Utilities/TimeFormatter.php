<?php
namespace Youkok\Common\Utilities;

class TimeFormatter
{

    public static function clean($d, $include_time = true)
    {
        if ($d == 'CURRENT_TIMESTAMP') {
            $d = date('Y-m-d  G:i:s');
        }

        $norwegian_months = ['jan', 'feb', 'mar', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep',
            'okt', 'nov', 'des'];

        $split1 = explode(' ', $d);
        $split2 = explode('-', $split1[0]);

        return (int) $split2[2] . '. ' . $norwegian_months[$split2[1] - 1] . ' ' . $split2[0] .
            ($include_time ? (' @ ' . $split1[1]) : '');
    }
}

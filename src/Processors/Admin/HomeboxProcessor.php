<?php
namespace Youkok\Processors\Admin;

use Youkok\Models\Download;
use Youkok\Models\Element;
use Youkok\Models\Session;
use Youkok\Utilities\NumberFormatter;

class HomeboxProcessor
{
    public static function run()
    {
        return [
            'downloads' => NumberFormatter::format(Download::count()),
            'sessions' => NumberFormatter::format(Session::count()),
            'elements' => NumberFormatter::format(Element
                ::where('directory', 0)
                ->count()),
            'courses' => NumberFormatter::format(Element
                ::where('directory', 1)
                ->where('parent', null)
                ->count())
        ];
    }
}

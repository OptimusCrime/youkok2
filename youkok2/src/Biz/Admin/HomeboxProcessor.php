<?php
namespace Youkok\Biz\Admin;

use Carbon\Carbon;
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
            'sessions_week' => NumberFormatter::format(Session
                ::where('last_updated', '>=', Carbon::now()->subWeek())
                ->count()),
            'sessions_day' => NumberFormatter::format(Session
                ::where('last_updated', '>=', Carbon::now()->subDay())
                ->count()),
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

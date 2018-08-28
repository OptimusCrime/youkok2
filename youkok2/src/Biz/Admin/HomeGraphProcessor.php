<?php
namespace Youkok\Biz\Admin;

use Carbon\Carbon;
use Youkok\Models\Download;
use Youkok\Models\Element;
use Youkok\Models\Session;
use Youkok\Utilities\NumberFormatter;

class HomeGraphProcessor
{
    const DAYS_BACK_IN_TIME = 30;

    public static function run()
    {
        return [
            'code' => 200,
            'data' => static::populateResponse(static::DAYS_BACK_IN_TIME)
        ];
    }

    private static function populateResponse($offset)
    {
        $data = [];
        for ($day = $offset; $day >= 0; $day--) {
            $date = date('Y-m-d', strtotime('-' . $day . ' days'));

            $data[] = [
                'date' => $date,
                'downloads' => Download::whereDate('downloaded_time', $date)->count()
            ];
        }

        return $data;
    }
}

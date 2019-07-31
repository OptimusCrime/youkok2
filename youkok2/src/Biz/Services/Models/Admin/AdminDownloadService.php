<?php

namespace Youkok\Biz\Services\Models\Admin;

use Illuminate\Database\Eloquent\Collection;
use Youkok\Common\Models\Download;

class AdminDownloadService
{
    public function getGroupedDownloadsForRange(int $range): Collection
    {
        return Download
            ::selectRaw('COUNT(id) as \'total\', DATE_FORMAT(downloaded_time, \'%Y-%m-%d\') dtime')
            ->whereRaw('downloaded_time BETWEEN CURDATE() - INTERVAL ' . $range . ' DAY AND CURDATE()')
            ->groupBy('dtime')
            ->get();
    }
}

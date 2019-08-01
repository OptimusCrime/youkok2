<?php

namespace Youkok\Biz\Services\Models\Admin;

use Illuminate\Database\Eloquent\Collection;
use Youkok\Common\Models\Download;

class AdminDownloadService
{
    public function getGroupedDownloadsForRange(int $range): Collection
    {
        // Why `+2` you ask? I have no idea, stupid MySQL...
        return Download
            ::selectRaw('COUNT(id) as \'total\', DATE_FORMAT(downloaded_time, \'%Y-%m-%d\') dtime')
            ->whereRaw('downloaded_time BETWEEN CURDATE() - INTERVAL ' . $range . ' DAY AND CURDATE() + 2')
            ->groupBy('dtime')
            ->get();
    }
}

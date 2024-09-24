<?php
namespace Youkok\Biz\Services\Models\Admin;

use Illuminate\Database\Capsule\Manager as DB;

class AdminDownloadService
{
    public function getDownloadsOnDate(string $date): int
    {
        return DB
            ::table("download")
            ->where('date', $date)
            ->sum('downloads');
    }
}

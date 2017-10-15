<?php
namespace Youkok\Controllers;

use Illuminate\Database\Capsule\Manager as DB;

use Youkok\Models\Download;

class DownloadController
{
    public static function getMostPopularElementsFromDelta($delta)
    {
        $query = DB::table('download')
            ->select('download.resource as id', DB::raw('COUNT(download.id) as download_count'))
            ->leftJoin('element as element', 'element.id', '=', 'download.resource')
            ->where('element.deleted', '=', 0)
            ->where('element.pending', '=', 0);

        $duration = Download::getMostPopularElementQueryFromDelta($delta);
        if ($duration !== null) {
            $query = $query->whereDate('download.downloaded_time', '>=', $duration);
        }

        return $query
            ->groupBy('download.resource')
            ->orderBy('download_count', 'DESC')
            ->orderBy('element.added', 'DESC')
            ->get();
    }

    public static function getMostPopularCoursesFromDelta($delta)
    {
        $downloads = static::getMostPopularElementsFromDelta($delta);
        // TODO
    }
}
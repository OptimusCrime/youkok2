<?php
namespace Youkok\Controllers;

use Illuminate\Database\Capsule\Manager as DB;

use Youkok\Models\Download;
use Youkok\Models\Element;

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

    public static function getMostPopularCoursesFromDelta($delta, $limit = null)
    {
        $result = static::summarizeDownloads(static::getMostPopularElementsFromDelta($delta));
        if ($limit === null) {
            return $result;
        }

        return array_slice($result, 0, $limit);
    }

    private static function summarizeDownloads($downloads)
    {
        $courses = [];
        foreach ($downloads as $download) {
            $downloadElement = Element::fromIdAll($download->id, ['id', 'parent']);
            if ($downloadElement === null) {
                continue;
            }

            $rootParent = $downloadElement->rootParentAll;
            if ($rootParent === null) {
                continue;
            }

            if (!isset($courses[$rootParent->id])) {
                $courses[$rootParent->id] = 0;
            }

            $courses[$rootParent->id] += $download->download_count;
        }

        arsort($courses);

        return static::transformResultArray($courses);
    }

    private static function transformResultArray($courses)
    {
        $newResult = [];
        foreach ($courses as $courseId => $courseDownloads) {
            $newResult[] = [
                'id' => $courseId,
                'downloads' => $courseDownloads
            ];
        }
        return $newResult;
    }
}
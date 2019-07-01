<?php
namespace Youkok\Biz\Services\Models;

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

use Illuminate\Support\Collection;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Common\Models\Download;
use Youkok\Common\Models\Element;
use Youkok\Enums\MostPopularElement;

class DownloadService
{
    public static function getDownloadsForId(int $id): int
    {
        return Download::select(DB::raw("COUNT(`id`) as `result`"))
            ->where('resource', $id)
            ->count();
    }

    public static function getNumberOfDownloads(): int
    {
        return Download::count();
    }

    // TODO type hinting
    public static function getLatestDownloads(int $limit): Collection
    {
        return DB::table('download')
            ->select(['downloaded_time', 'element.*'])
            ->leftJoin('element as element', 'element.id', '=', 'download.resource')
            ->orderBy('downloaded_time', 'DESC')
            ->limit($limit)
            ->get();
    }

    public static function getMostPopularElementsFromDelta(string $delta): Collection
    {
        $query = DB::table('download')
            ->select('download.resource as id', DB::raw('COUNT(download.id) as download_count'))
            ->leftJoin('element as element', 'element.id', '=', 'download.resource')
            ->where('element.deleted', '=', 0)
            ->where('element.pending', '=', 0);

        if ($delta !== MostPopularElement::ALL) {
            $query = $query->whereDate(
                'download.downloaded_time',
                '>=',
                static::getMostPopularElementQueryFromDelta($delta)
            );
        }

        return $query
            ->groupBy('download.resource')
            ->orderBy('download_count', 'DESC')
            ->orderBy('element.added', 'DESC')
            ->get();
    }

    public static function getMostPopularCoursesFromDelta(string $delta, int $limit)
    {
        $result = static::summarizeDownloads(static::getMostPopularElementsFromDelta($delta));

        return array_slice($result, 0, $limit);
    }

    public static function newDownloadForElement(Element $element)
    {
        $download = new Download();
        $download->resource = $element->id;
        $download->ip = $_SERVER['REMOTE_ADDR'];
        $download->agent = $_SERVER['HTTP_USER_AGENT'];
        $download->downloaded_time = Carbon::now();
        $download->save();
    }

    private static function summarizeDownloads($downloads)
    {
        $courses = [];
        foreach ($downloads as $download) {
            $downloadElement = Element::fromIdAll($download->id, ['id', 'parent']);
            if ($downloadElement === null) {
                continue;
            }

            $rootParent = $downloadElement->getRootParentAll();
            if ($rootParent === null) {
                continue;
            }

            if (!isset($courses[$rootParent->id])) {
                $courses[$rootParent->id] = 0;
            }

            $courses[$rootParent->id] += $download->download_count;
        }

        // Make sure to filter out all courses that are either deleted or filtered away
        $courses = static::filterRemovedCourses($courses);

        arsort($courses);

        return static::transformResultArray($courses);
    }

    private static function filterRemovedCourses(array $courses): array
    {
        $filteredCourses = [];
        foreach ($courses as $courseId => $downloads) {
            if (static::isValidCourseId($courseId)) {
                $filteredCourses[$courseId] = $downloads;
            }
        }

        return $filteredCourses;
    }

    private static function isValidCourseId(int $courseId): bool
    {
        $element = Element
            ::select('id')
            ->where('id', $courseId)
            ->where('parent', null)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->get();

        return count($element) !== 0;
    }

    private static function transformResultArray(array $courses): array
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

    private static function getMostPopularElementQueryFromDelta(string $delta): Carbon
    {
        switch ($delta) {
            case MostPopularElement::DAY:
                return Carbon::now()->subDay();
            case MostPopularElement::WEEK:
                return Carbon::now()->subWeek();
            case MostPopularElement::MONTH:
                return Carbon::now()->subMonth();
            case MostPopularElement::YEAR:
                return Carbon::now()->subYear();
            default:
                throw new GenericYoukokException('Invalid delta');
        }
    }
}

<?php
namespace Youkok\Biz\Services\Models;

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

use Illuminate\Support\Collection;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Common\Models\Download;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Enums\MostPopularElement;

class DownloadService
{
    private $elementService;

    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    public function getDownloadsForId(int $id): int
    {
        return Download::select(DB::raw("COUNT(`id`) as `result`"))
            ->where('resource', $id)
            ->count();
    }

    public function getNumberOfDownloads(): int
    {
        return Download::count();
    }

    public function getLatestDownloads(int $limit): array
    {
        $downloads = DB::table('download')
            ->select(['downloaded_time', 'element.id'])
            ->leftJoin('element as element', 'element.id', '=', 'download.resource')
            ->orderBy('downloaded_time', 'DESC')
            ->limit($limit)
            ->get();

        $response = [];
        foreach ($downloads as $download) {
            $element = $this->elementService->getElement(
                new SelectStatements('id', $download->id),
                ['id', 'name', 'slug', 'uri'],
                [
                    ElementService::FLAG_FETCH_COURSE
                ]
            );

            $element->setDownloadedTime($download->downloaded_time);

            $response[] = $element;
        }

        return $response;
    }

    public function getMostPopularElementsFromDelta(string $delta): Collection
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
                $this->getMostPopularElementQueryFromDelta($delta)
            );
        }

        return $query
            ->groupBy('download.resource')
            ->orderBy('download_count', 'DESC')
            ->orderBy('element.added', 'DESC')
            ->get();
    }

    public function getMostPopularCoursesFromDelta(string $delta, int $limit)
    {
        $result = $this->summarizeDownloads($this->getMostPopularElementsFromDelta($delta));

        return array_slice($result, 0, $limit);
    }

    public function newDownloadForElement(Element $element): bool
    {
        $download = new Download();
        $download->resource = $element->id;
        $download->ip = $_SERVER['REMOTE_ADDR'];
        $download->agent = $_SERVER['HTTP_USER_AGENT'];
        $download->downloaded_time = Carbon::now();
        return $download->save();
    }

    private function summarizeDownloads(Collection $downloads): array
    {
        $courses = [];
        foreach ($downloads as $download) {
            $downloadElement = $this->elementService->getElement(
                new SelectStatements('id', $download->id),
                ['id', 'parent'],
                [
                    ElementService::FLAG_FETCH_PARENTS,
                    ElementService::FLAG_FETCH_COURSE
                ]
            );

            $downloadElementCourse = $downloadElement->getCourse();

            // Weird guard, I do not know why, but it might seem like some courses have downloads on them? No idea
            // how that happened, but this guard should take care of it. Might be an artifact from ages ago.
            if ($downloadElementCourse === null) {
                continue;
            }

            if (!isset($courses[$downloadElementCourse->id])) {
                $courses[$downloadElementCourse->id] = 0;
            }

            $courses[$downloadElementCourse->id] += $download->download_count;
        }

        // Make sure to filter out all courses that are either deleted or filtered away
        $courses = $this->filterRemovedCourses($courses);

        arsort($courses);

        return static::transformResultArray($courses);
    }

    private function filterRemovedCourses(array $courses): array
    {
        $filteredCourses = [];
        foreach ($courses as $courseId => $downloads) {
            if ($this->isValidCourseId((int) $courseId)) {
                $filteredCourses[$courseId] = $downloads;
            }
        }

        return $filteredCourses;
    }

    private function isValidCourseId(int $courseId): bool
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

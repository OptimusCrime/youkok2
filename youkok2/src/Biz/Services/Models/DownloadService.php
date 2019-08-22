<?php
namespace Youkok\Biz\Services\Models;

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

use Illuminate\Support\Collection;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Common\Models\Download;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Enums\MostPopularCourse;
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

    public function getMostPopularElementsFromDelta(string $delta, ?int $limit = null): Collection
    {
        $query = DB::table('download')
            ->select(
                'download.resource AS id',
                'element.parent AS parent',
                'element.pending AS pending',
                'element.deleted AS deleted',
                DB::raw('COUNT(download.id) AS download_count')
            )
            ->leftJoin('element AS element', 'element.id', '=', 'download.resource');

        if ($delta !== MostPopularElement::ALL && $delta !== MostPopularCourse::ALL) {
            $query = $query->whereDate(
                'download.downloaded_time',
                '>=',
                $this->getMostPopularElementQueryFromDelta($delta)
            );
        }

        if ($limit !== null) {
            $query = $query->limit($limit);
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
        $parentToCourse = [];
        $courses = [];

        foreach ($downloads as $download) {
            $downloadElement = null;
            $currentCourse = null;

            try {
                if (isset($parentToCourse[$download->parent])) {
                    // We can completely avoid fetching all parents for the Element if we already have etablished
                    // the relationship between the parent -> course.
                    $selectStatements = new SelectStatements();
                    $selectStatements->addStatement('id', $download->id);
                    $selectStatements->addStatement('deleted', 0);
                    $selectStatements->addStatement('pending', 0);

                    // Ensure the element is visible
                    $this->elementService->getElement(
                        $selectStatements,
                        ['id', 'parent'],
                        []
                    );

                    $currentCourse = $parentToCourse[$download->parent];
                } else {
                    $downloadElement = $this->elementService->getElement(
                        new SelectStatements('id', $download->id),
                        ['id', 'parent'],
                        [
                            ElementService::FLAG_FETCH_PARENTS,
                            ElementService::FLAG_FETCH_COURSE,
                            ElementService::FLAG_ENSURE_VISIBLE,
                        ]
                    );

                    $currentCourseObject = $downloadElement->getCourse();

                    // Weird guard, I do not know why, but it might seem like some courses have downloads on them?
                    // No idea how that happened, but this guard should take care of it. Might be an artifact
                    // from ages ago.
                    if ($currentCourseObject === null) {
                        continue;
                    }

                    $parentToCourse[$download->parent] = $currentCourseObject->id;

                    $currentCourse = $currentCourseObject->id;
                }
            } catch (ElementNotFoundException $ex) {
                // This is to be expected, if we found a popular download, that is later deleted (or their parents are)
                // we should just ignore this and keep going.
                continue;
            }

            if (!isset($courses[$currentCourse])) {
                $courses[$currentCourse] = 0;
            }

            $courses[$currentCourse] += $download->download_count;
        }

        // Make sure to filter out all courses that are either deleted or filtered away
        $courses = $this->filterRemovedCourses($courses);

        arsort($courses);

        return static::transformResultArray($courses);
    }

    private function filterRemovedCourses(array $courses): array
    {
        $validCourseIds = [];
        $filteredCourses = [];
        foreach ($courses as $courseId => $downloads) {
            $inValidCoursesIds = in_array($courseId, $validCourseIds);
            if ($inValidCoursesIds || $this->isValidCourseId((int) $courseId)) {
                $filteredCourses[$courseId] = $downloads;

                if (!$inValidCoursesIds) {
                    $validCourseIds[] = $courseId;
                }
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

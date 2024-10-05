<?php
namespace Youkok\Biz\Services\Job\Jobs;

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use RedisException;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

class UpdateDownloadsJobService implements JobServiceInterface
{
    private CacheService $cacheService;
    private ElementService $elementService;

    public function __construct(
        CacheService $cacheService,
        ElementService $elementService,
    )
    {
        $this->cacheService = $cacheService;
        $this->elementService = $elementService;
    }

    /**
     * @throws RedisException
     * @throws ElementNotFoundException
     */
    public function run(): void
    {
        $this->deleteOldDownloads();

        $elementsToUpdate = $this->getElementsToUpdate();
        $this->updateDownloadsForElements($elementsToUpdate);

        $allCourses = $this->getAllCoursesToUpdate();
        $this->updateCourses($allCourses);

        $this->clearMostPopularCaches();
    }

    private function deleteOldDownloads(): void
    {
        DB::statement("DELETE FROM download WHERE date < (now() - ('366 days'::INTERVAL))");
    }

    private function getElementsToUpdate(): Collection
    {
        return DB::table("download")->select('element')->distinct()->get();
    }

    /**
     * @throws ElementNotFoundException
     */
    private function updateDownloadsForElements(Collection $elementsToUpdate): void
    {
        foreach ($elementsToUpdate as $el) {
            $id = $el->element;

            $element = $this->elementService->getElement(new SelectStatements('id', $id));
            $element->downloads_today = $this->getDownloadCount($id, 1);
            $element->downloads_week = $this->getDownloadCount($id, 7);
            $element->downloads_month = $this->getDownloadCount($id, 30);
            $element->downloads_year = $this->getDownloadCount($id, 365);
            $element->save();
        }
    }

    private function updateCourses(Collection $courses): void
    {
        /** @var Element $course */
        foreach ($courses as $course) {
            $container = new CourseDownloadsContainer($course->id);
            $container->run();

            $course->downloads_today = $container->getDownloadsToday();
            $course->downloads_week = $container->getDownloadsWeek();
            $course->downloads_month = $container->getDownloadsMonth();
            $course->downloads_year = $container->getDownloadsYear();
            $course->save();
        }
    }

    private function getDownloadCount(int $elementId, int $subDays): int
    {
        return DB
            ::table("download")
            ->where('element', $elementId)
            ->where('date', '>=', Carbon::now()->sub($subDays . ' day')->format('Y-m-d'))
            ->sum('downloads');
    }

    private function getAllCoursesToUpdate(): Collection
    {
        return Element::select(Element::ALL_FIELDS)
            ->where('parent', null)
            ->where('directory', true)
            ->where('deleted', false)
            ->where('empty', false)
            ->orderBy('name')
            ->get();
    }

    /**
     * @throws RedisException
     */
    private function clearMostPopularCaches(): void
    {
        foreach (MostPopularElement::collection() as $delta) {
            $this->cacheService->delete(CacheKeyGenerator::keyForMostPopularElementsSetForDelta($delta));
            $this->cacheService->delete(CacheKeyGenerator::keyForMostPopularElementsForDelta($delta));
        }

        foreach (MostPopularCourse::collection() as $delta) {
            $this->cacheService->delete(CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta));
        }
    }
}

<?php
namespace Youkok\Biz\Services\Models;

use Carbon\Carbon;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use RedisException;
use Youkok\Biz\Services\CacheService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;

class CourseService
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function getNumberOfVisibleCourses(): int
    {
        return Element
            ::where('directory', true)
            ->where('parent', null)
            ->where('deleted', false)
            ->where('empty', false)
            ->count();
    }

    public function getAllCourses(): Collection
    {
        return Element::select(Element::ALL_FIELDS)
            ->where('parent', null)
            ->where('directory', true)
            ->where('deleted', false)
            ->orderBy('name')
            ->get();
    }

    /**
     * @throws RedisException
     */
    public function getLastVisitedCourses(int $limit): Collection
    {
        return Element::select(Element::ALL_FIELDS)
            ->where('directory', true)
            ->where('deleted', false)
            ->whereNull('parent')
            ->whereNotNull('last_visited')
            ->orderBy('last_visited', 'DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * @throws RedisException
     * @throws Exception
     */
    public function updateLastVisited(Element $element): void
    {
        if ($element->isCourse()) {
            $element->last_visited = Carbon::now();
            $element->save();
        }
        else {
            $course = $element->getCourse();
            $course->last_visited = Carbon::now();
            $course->save();
        }

        $this->cacheService->delete(CacheKeyGenerator::keyForLastVisitedCoursesPayload());
    }
}

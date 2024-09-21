<?php
namespace Youkok\Biz\Services\Models;

use Carbon\Carbon;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use RedisException;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\UrlService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;

class CourseService
{
    const int LAST_VISITED_COURSES_SET_SIZE = 10;

    private CacheService $cacheService;
    private UrlService $urlService;

    public function __construct(CacheService $cacheService, UrlService $urlService)
    {
        $this->cacheService = $cacheService;
        $this->urlService = $urlService;
    }

    public function getNumberOfVisibleCourses(): int
    {
        return Element
            ::where('directory', 1)
            ->where('parent', null)
            ->where('deleted', 0)
            ->where('empty', 0)
            ->count();
    }

    public function getAllCourses(): Collection
    {
        return Element::select('id', 'name', 'slug', 'empty')
            ->where('parent', null)
            ->where('directory', 1)
            ->orderBy('name')
            ->get();
    }

    /**
     * @throws RedisException
     */
    public function getLastVisitedCourses(): array
    {
        $set = $this->cacheService->getSortedRangeByKey(
            CacheKeyGenerator::keyForLastVisitedCourseSet()
        );

        $output = [];
        foreach ($set as $member => $visited) {
            $arr = json_decode($member, true);
            $arr['last_visited'] = $visited;

            $output[] = $arr;
        }

        return $output;
    }

    /**
     * @throws RedisException
     * @throws Exception
     */
    public function updateLastVisited(RouteParserInterface $routeParser, Element $element): void
    {
        $key = CacheKeyGenerator::keyForLastVisitedCourseSet();

        $member = json_encode([
            'id' => $element->id,
            'courseCode' => $element->getCourseCode(),
            'courseName' => $element->getCourseName(),
            'url' => $this->urlService->urlForCourse($routeParser, $element),
        ]);

        // Get the current set
        $set = $this->cacheService->getSortedRangeByKey($key);

        $currentTimestamp = Carbon::now()->timestamp;

        // If the set contains the current element, increase that value with the timestamp difference
        if (isset($set[$member])) {
            $valueDifference = $currentTimestamp - $set[$member];
            $this->cacheService->updateValueInSet($key, $valueDifference, $member);
            return;
        }

        // The current member does not exist. We have to insert it
        $this->cacheService->insertIntoSet(
            $key,
            $currentTimestamp, // Value equals the current timestamp
            $member
        );

        // If the set contains ten members before we added the new member, delete the last one
        if (count($set) >= static::LAST_VISITED_COURSES_SET_SIZE) {

            $this->cacheService->removeFromSetByRank(
                $key,
                0,
                0
            );
        }
    }
}

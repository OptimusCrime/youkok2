<?php

namespace Youkok\Biz\Services\Models;

use Illuminate\Database\Eloquent\Collection;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\UrlService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;

class CourseService
{
    const LAST_VISITED_COURSES_SET_SIZE = 10;

    private ElementService $elementService;
    private CacheService $cacheService;
    private UrlService $urlService;

    public function __construct(ElementService $elementService, CacheService $cacheService, UrlService $urlService)
    {
        $this->elementService = $elementService;
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
     * @param Element $element
     * @throws GenericYoukokException
     */
    public function updateLastVisited(Element $element): void
    {
        $key = CacheKeyGenerator::keyForLastVisitedCourseSet();

        $member = json_encode([
            'id' => $element->id,
            'courseCode' => $element->getCourseCode(),
            'courseName' => $element->getCourseName(),
            'url' => $this->urlService->urlForCourse($element),
        ]);

        // Get the current set
        $set = $this->cacheService->getSortedRangeByKey($key);

        // If the set contains the current element, increase that value with the timestamp difference
        if (isset($set[$member])) {
            $valueDifference = time() - $set[$member];
            $this->cacheService->updateValueInSet($key, $valueDifference, $member);
            return;
        }

        // The current member does not exist. We have to insert it
        $this->cacheService->insertIntoSet(
            $key,
            time(), // Value equals the current timestamp
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

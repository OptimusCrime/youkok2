<?php
namespace Youkok\Biz\Services\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Common\Models\Element;

class CourseService
{
    private $elementService;

    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    public function getNumberOfNonVisibleCourses(): int
    {
        return Element
            ::where('directory', 1)
            ->where('parent', null)
            ->where('deleted', 0)
            ->where('empty', 0)
            ->count();
    }

    public function getAllVisibleCourses(): Collection
    {
        return Element::select('id', 'name', 'slug', 'empty')
            ->where('parent', null)
            ->where('directory', 1)
            ->orderBy('name')
            ->get();
    }

    public function getLastVisitedCourses($limit = 10): Collection
    {
        return Element::select('id', 'name', 'slug', 'uri', 'last_visited')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('last_visited', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function updateLastVisible(Element $element): void
    {
        $element->last_visited = Carbon::now();
        $element->save();
    }
}

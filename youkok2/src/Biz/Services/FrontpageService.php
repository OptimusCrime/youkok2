<?php

namespace Youkok\Biz\Services;

use Redis;

use Youkok\Biz\Services\Course\CourseService;
use Youkok\Biz\Services\Download\DownloadService;
use Youkok\Biz\Services\Element\ElementService;
use Youkok\Biz\Services\User\UserService;
use Youkok\Common\Controllers\ElementController;
use Youkok\Common\Models\Element;
use Youkok\Biz\Services\PopularListing\PopularCoursesService;
use Youkok\Biz\Services\PopularListing\PopularElementsService;

class FrontpageService
{
    const PROCESSORS_LIMIT = 10;

    private $sessionService;
    private $cache;

    private $popularCoursesProcessor;
    private $popularElementsProcessor;

    private $elementService;
    private $downloadService;
    private $courseService;
    private $userService;

    public function __construct(
        SessionService $sessionService,
        Redis $cache,

        PopularCoursesService $popularCoursesProcessor,
        PopularElementsService $popularElementsProcessor,

        ElementService $elementService,
        DownloadService $downloadService,
        CourseService $courseService,
        UserService $userService
    )
    {
        $this->sessionService = $sessionService;
        $this->cache = $cache;

        $this->popularCoursesProcessor = $popularCoursesProcessor;
        $this->popularElementsProcessor = $popularElementsProcessor;

        $this->elementService = $elementService;
        $this->downloadService = $downloadService;
        $this->courseService = $courseService;
        $this->userService = $userService;
    }

    public function get()
    {
        return [
            'number_files' => $this->elementService->getNumberOfVisibleFiles(),
            'number_downloads' => $this->downloadService->getNumberOfDownloads(),
            'number_courses_with_content' => $this->courseService->getNumberOfNonVisibleCourses(),
            'number_new_elements' => $this->elementService->getNumberOfFilesThisMonth(),
            'elements_new' => ElementController::getLatest(static::PROCESSORS_LIMIT),
            'courses_last_visited' => $this->getLastVisitedCourses(static::PROCESSORS_LIMIT),

            'elements_most_popular' => $this->popularElementsProcessor->run(static::PROCESSORS_LIMIT),
            'courses_most_popular' => $this->popularCoursesProcessor->run(static::PROCESSORS_LIMIT),

            'user_preferences' => $this->userService->getUserPreferences(),
            'user_favorites' => array_reverse($this->userService->getUserListing(UserService::FAVORITES)),
            'user_last_visited_courses' => $this->userService->getUserListing(UserService::LATEST_COURSE_VISITED),
        ];
    }

    public function getLastVisitedCourses($limit = 10)
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
}

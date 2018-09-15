<?php

namespace Youkok\Biz\Services;

use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\Course\CourseService;
use Youkok\Biz\Services\Download\DownloadService;
use Youkok\Biz\Services\Element\ElementService;
use Youkok\Biz\Services\User\UserService;
use Youkok\Common\Models\Element;
use Youkok\Biz\Services\PopularListing\PopularCoursesService;
use Youkok\Biz\Services\PopularListing\PopularElementsService;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

class FrontpageService
{
    const PROCESSORS_LIMIT = 10;
    const FRONTPAGE_CHANGE_PARAM = 'type';
    const FRONTPAGE_RESET_HISTORY = 'history';
    const FRONTPAGE_RESET_FAVORITES = 'favorites';

    private $sessionService;

    private $popularCoursesProcessor;
    private $popularElementsProcessor;

    private $elementService;
    private $downloadService;
    private $courseService;
    private $userService;

    public function __construct(
        SessionService $sessionService,

        PopularCoursesService $popularCoursesProcessor,
        PopularElementsService $popularElementsProcessor,

        ElementService $elementService,
        DownloadService $downloadService,
        CourseService $courseService,
        UserService $userService
    )
    {
        $this->sessionService = $sessionService;

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
            'latest_elements' => $this->elementService->getLatestElements(static::PROCESSORS_LIMIT),
            'courses_last_visited' => $this->getLastVisitedCourses(static::PROCESSORS_LIMIT),

            'elements_most_popular' => $this->popularElementsProcessor->run(MostPopularElement::ALL, static::PROCESSORS_LIMIT),
            'courses_most_popular' => $this->popularCoursesProcessor->run(MostPopularCourse::ALL, static::PROCESSORS_LIMIT),

            'user_preferences' => $this->userService->getUserPreferences(),
            'user_favorites' => array_reverse($this->userService->getUserListing(UserService::FAVORITES)),
            'user_history' => $this->userService->getUserListing(UserService::HISTORY),
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

    public function resetFrontpageBox($type)
    {
        die($type);
        if (!static::isValidResetRequest($type)) {
            throw new InvalidRequestException();
        }

        if ($type === static::FRONTPAGE_RESET_HISTORY) {
            $this->sessionService->setData('latest_course_visited', [], SessionService::MODE_OVERWRITE);
        }
        else {
            $this->sessionService->setData('favorites', [], SessionService::MODE_OVERWRITE);
        }

        $this->sessionService->store(true);

        return true;
    }

    private static function isValidResetRequest($type)
    {
        switch ($type) {
            case static::FRONTPAGE_RESET_HISTORY:
            case static::FRONTPAGE_RESET_FAVORITES:
                return true;
            default:
                return false;
        }
    }
}

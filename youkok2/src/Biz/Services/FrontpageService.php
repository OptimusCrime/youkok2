<?php

namespace Youkok\Biz\Services;

use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\User\UserService;
use Youkok\Common\Controllers\CourseController;
use Youkok\Common\Controllers\DownloadController;
use Youkok\Common\Controllers\ElementController;
use Youkok\Biz\Services\PopularListing\MostPopularCoursesService;
use Youkok\Biz\Services\PopularListing\MostPopularElementsService;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

class FrontpageService
{
    const PROCESSORS_LIMIT = 10;

    const FRONTPAGE_PUT_TYPE_PARAM = 'delta';
    const FRONTPAGE_PUT_VALUE_PARAM = 'value';

    private $sessionService;

    private $popularCoursesProcessor;
    private $popularElementsProcessor;

    private $userService;

    public function __construct(
        SessionService $sessionService,

        MostPopularCoursesService $popularCoursesProcessor,
        MostPopularElementsService $popularElementsProcessor,

        UserService $userService
    ) {
        $this->sessionService = $sessionService;

        $this->popularCoursesProcessor = $popularCoursesProcessor;
        $this->popularElementsProcessor = $popularElementsProcessor;

        $this->userService = $userService;
    }

    public function get()
    {
        return [
            'number_files' => ElementController::getNumberOfVisibleFiles(),
            'number_downloads' => DownloadController::getNumberOfDownloads(),
            'number_courses_with_content' => CourseController::getNumberOfNonVisibleCourses(),
            'number_new_elements' => ElementController::getNumberOfFilesThisMonth(),
            'latest_elements' => ElementController::getLatestElements(static::PROCESSORS_LIMIT),
            'courses_last_visited' => CourseController::getLastVisitedCourses(static::PROCESSORS_LIMIT),
            'last_downloaded' => DownloadController::getLatestDownloads(static::PROCESSORS_LIMIT),

            'elements_most_popular' => $this->popularElementsProcessor->fromDelta(MostPopularElement::ALL, static::PROCESSORS_LIMIT),
            'courses_most_popular' => $this->popularCoursesProcessor->fromDelta(MostPopularCourse::ALL, static::PROCESSORS_LIMIT),

            'user_preferences' => $this->userService->getUserPreferences(),
        ];
    }

    public function put($type, $value)
    {
        if (!in_array($type, [UserService::DELTA_POST_POPULAR_ELEMENTS, UserService::DELTA_POST_POPULAR_COURSES])) {
            throw new InvalidRequestException();
        }

        if ($type === UserService::DELTA_POST_POPULAR_ELEMENTS && !in_array($value, MostPopularElement::all())) {
            throw new InvalidRequestException();
        }

        if ($type === UserService::DELTA_POST_POPULAR_COURSES && !in_array($value, MostPopularCourse::all())) {
            throw new InvalidRequestException();
        }

        // Update user preferences
        $this->sessionService->forceSetData(UserService::USER_PREFERENCE_LOOKUP[$type], $value);

        // Return the new value
        if ($type === UserService::DELTA_POST_POPULAR_ELEMENTS) {
            return $this->popularElementsProcessor->fromDelta($value, static::PROCESSORS_LIMIT);
        }

        return $this->popularCoursesProcessor->fromDelta($value, static::PROCESSORS_LIMIT);
    }
}

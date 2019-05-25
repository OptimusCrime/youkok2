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
    const SERVICE_LIMIT = 10;

    const FRONTPAGE_PUT_DELTA_PARAM = 'delta';
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
        $userPreferenceMostPopularElement = $this->userService->getUserMostPopularPreference(
            UserService::DELTA_POST_POPULAR_ELEMENTS,
            MostPopularElement::ALL
        );

        $userPreferenceMostPopularCourse = $this->userService->getUserMostPopularPreference(
            UserService::DELTA_POST_POPULAR_COURSES,
            MostPopularCourse::ALL
        );

        return [
            'number_files' => ElementController::getNumberOfVisibleFiles(),
            'number_downloads' => DownloadController::getNumberOfDownloads(),
            'number_courses_with_content' => CourseController::getNumberOfNonVisibleCourses(),
            'number_new_elements' => ElementController::getNumberOfFilesThisMonth(),
            'latest_elements' => ElementController::getLatestElements(static::SERVICE_LIMIT),
            'courses_last_visited' => CourseController::getLastVisitedCourses(static::SERVICE_LIMIT),
            'last_downloaded' => DownloadController::getLatestDownloads(static::SERVICE_LIMIT),

            'elements_most_popular' => $this->popularElementsProcessor->fromDelta(
                $userPreferenceMostPopularElement,
                static::SERVICE_LIMIT
            ),

            'courses_most_popular' => $this->popularCoursesProcessor->fromDelta(
                $userPreferenceMostPopularCourse,
                static::SERVICE_LIMIT
            ),

            'user_preferences' => $this->userService->getUserPreferences(),
        ];
    }

    public function put($delta, $value)
    {
        if (!in_array($delta, [UserService::DELTA_POST_POPULAR_ELEMENTS, UserService::DELTA_POST_POPULAR_COURSES])) {
            throw new InvalidRequestException();
        }

        if ($delta === UserService::DELTA_POST_POPULAR_ELEMENTS && !in_array($value, MostPopularElement::all())) {
            throw new InvalidRequestException();
        }

        if ($delta === UserService::DELTA_POST_POPULAR_COURSES && !in_array($value, MostPopularCourse::all())) {
            throw new InvalidRequestException();
        }

        // Update user preferences
        $this->sessionService->setData(UserService::USER_PREFERENCE_LOOKUP[$delta], $value);

        // Return the new value
        if ($delta === UserService::DELTA_POST_POPULAR_ELEMENTS) {
            return $this->popularElementsProcessor->fromDelta($value, static::SERVICE_LIMIT);
        }

        return $this->popularCoursesProcessor->fromDelta($value, static::SERVICE_LIMIT);
    }
}

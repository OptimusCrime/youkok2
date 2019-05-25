<?php

namespace Youkok\Biz\Services;

use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Common\Controllers\CourseController;
use Youkok\Common\Controllers\DownloadController;
use Youkok\Common\Controllers\ElementController;
use Youkok\Biz\Services\PopularListing\MostPopularCoursesService;
use Youkok\Biz\Services\PopularListing\MostPopularElementsService;
use Youkok\Common\Models\Session;
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

    public function __construct(
        SessionService $sessionService,
        MostPopularCoursesService $popularCoursesProcessor,
        MostPopularElementsService $popularElementsProcessor
    ) {
        $this->sessionService = $sessionService;

        $this->popularCoursesProcessor = $popularCoursesProcessor;
        $this->popularElementsProcessor = $popularElementsProcessor;
    }

    public function get()
    {
        $session = $this->sessionService->getSession();

        return [
            'number_files' => ElementController::getNumberOfVisibleFiles(),
            'number_downloads' => DownloadController::getNumberOfDownloads(),
            'number_courses_with_content' => CourseController::getNumberOfNonVisibleCourses(),
            'number_new_elements' => ElementController::getNumberOfFilesThisMonth(),
            'latest_elements' => ElementController::getLatestElements(static::SERVICE_LIMIT),
            'courses_last_visited' => CourseController::getLastVisitedCourses(static::SERVICE_LIMIT),
            'last_downloaded' => DownloadController::getLatestDownloads(static::SERVICE_LIMIT),

            'elements_most_popular' => $this->popularElementsProcessor->fromDelta(
                $session->getMostPopularElement(),
                static::SERVICE_LIMIT
            ),

            'courses_most_popular' => $this->popularCoursesProcessor->fromDelta(
                $session->getMostPopularCourse(),
                static::SERVICE_LIMIT
            ),

            'user_preferences' => $session->getUserPreferences(),
        ];
    }

    public function put(string $delta, string $value)
    {
        if (!in_array($delta, [Session::KEY_MOST_POPULAR_ELEMENT, Session::KEY_MOST_POPULAR_COURSE])) {
            throw new InvalidRequestException();
        }

        if ($delta === Session::KEY_MOST_POPULAR_ELEMENT && !in_array($value, MostPopularElement::all())) {
            throw new InvalidRequestException();
        }

        if ($delta === Session::KEY_MOST_POPULAR_COURSE && !in_array($value, MostPopularCourse::all())) {
            throw new InvalidRequestException();
        }

        $session = $this->sessionService->getSession();

        if ($delta === Session::KEY_MOST_POPULAR_ELEMENT) {
            $session->setMostPopularElement($value);

            return $this->popularElementsProcessor->fromDelta($value, static::SERVICE_LIMIT);
        }

        $session->setMostPopularCourse($value);

        return $this->popularCoursesProcessor->fromDelta($value, static::SERVICE_LIMIT);
    }
}

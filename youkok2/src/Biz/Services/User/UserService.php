<?php
namespace Youkok\Biz\Services\User;

use Youkok\Biz\Services\SessionService;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

class UserService
{
    const DELTA_POST_POPULAR_ELEMENTS = 'DELTA_POST_POPULAR_ELEMENTS';
    const DELTA_POST_POPULAR_COURSES = 'DELTA_POST_POPULAR_COURSES';

    const USER_PREFERENCE_LOOKUP = [
        UserService::DELTA_POST_POPULAR_ELEMENTS => 'most_popular_element',
        UserService::DELTA_POST_POPULAR_COURSES => 'most_popular_course',
    ];

    private $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function getUserPreference($key, $default = null)
    {
        return $this->sessionService->getData($key, $default);
    }

    public function getUserMostPopularPreference($key, $default = null)
    {
        return $this->getUserPreference(static::USER_PREFERENCE_LOOKUP[$key], $default);
    }

    public function getUserPreferences()
    {
        return [
            static::DELTA_POST_POPULAR_ELEMENTS => $this->getUserMostPopularPreference(
                static::DELTA_POST_POPULAR_ELEMENTS,
                MostPopularElement::MONTH
            ),

            static::DELTA_POST_POPULAR_COURSES => $this->getUserMostPopularPreference(
                static::DELTA_POST_POPULAR_COURSES,
                MostPopularCourse::MONTH
            ),
        ];
    }
}

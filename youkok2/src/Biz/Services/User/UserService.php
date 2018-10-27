<?php
namespace Youkok\Biz\Services\User;

use Youkok\Biz\Services\SessionService;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

class UserService
{
    const FAVORITES = 'favorites';
    const HISTORY = 'history';

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

    public function getUserPreferences()
    {
        $keyElementsMostPopular = static::USER_PREFERENCE_LOOKUP[static::DELTA_POST_POPULAR_ELEMENTS];
        $keyCoursesMostPopular = static::USER_PREFERENCE_LOOKUP[static::DELTA_POST_POPULAR_COURSES];
        return [
            static::DELTA_POST_POPULAR_ELEMENTS => $this->sessionService->getData($keyElementsMostPopular, MostPopularElement::MONTH),
            static::DELTA_POST_POPULAR_COURSES => $this->sessionService->getData($keyCoursesMostPopular, MostPopularCourse::MONTH),
        ];
    }
}
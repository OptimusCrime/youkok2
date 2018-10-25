<?php
namespace Youkok\Biz\Services\User;

use Youkok\Biz\Services\SessionService;
use Youkok\Enums\MostPopularElement;

class UserService
{
    const FAVORITES = 'favorites';
    const HISTORY = 'history';

    private $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function getUserPreferences()
    {
        return [
            'DELTA_POST_POPULAR_ELEMENTS' => $this->getUserPreferenceForKey('most_popular_element'),
            'DELTA_POST_POPULAR_COURSES' => $this->getUserPreferenceForKey('most_popular_course'),
        ];
    }

    private function getUserPreferenceForKey($key)
    {
        $frontpageSettings = $this->sessionService->getDataWithKey('frontpage');
        if ($frontpageSettings === null or !is_array($frontpageSettings)) {
            return MostPopularElement::MONTH;
        }

        if (!isset($frontpageSettings[$key])) {
            return MostPopularElement::MONTH;
        }

        return $frontpageSettings[$key];
    }
}
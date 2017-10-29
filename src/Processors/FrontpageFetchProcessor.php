<?php
namespace Youkok\Processors;

use Carbon\Carbon;

use Youkok\Controllers\ElementController;
use Youkok\Enums\MostPopularElement;
use Youkok\Helpers\SessionHandler;
use Youkok\Models\Download;
use Youkok\Models\Element;
use Youkok\Processors\PopularListing\PopularCoursesProcessor;
use Youkok\Processors\PopularListing\PopularElementsProcessor;

class FrontpageFetchProcessor
{
    const PROCESSORS_LIMIT = 10;
    const FAVORITES = 'favorites';
    const LATEST_COURSE_VISITED = 'latest_course_visited';

    private $sessionHandler;
    private $cache;
    private $settings;

    public function __construct(SessionHandler $sessionHandler)
    {
        $this->sessionHandler = $sessionHandler;
        return $this;
    }

    public function withCache($cache)
    {
        $this->cache = $cache;
        return $this;
    }

    public function withSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    public function run()
    {

        return [
            'INFO_FILES' => Element
                ::where('directory', 0)
                ->where('deleted', 0)
                ->where('pending', 0)
                ->count(),
            'INFO_DOWNLOADS' => Download::count(),
            'INFO_NON_EMPTY_COURSES' => Element
                ::where('directory', 1)
                ->where('parent', null)
                ->where('deleted', 0)
                ->where('empty', 0)
                ->count(),
            'INFO_NEW_ELEMENTS_THIS_MONTH' => Element
                ::where('directory', 0)
                ->where('deleted', 0)
                ->whereDate('added', '>=', Carbon::now()->subMonth())
                ->count(),
            'LATEST_ELEMENTS' => ElementController::getLatest(static::PROCESSORS_LIMIT),
            'MOST_POPULAR_ELEMENTS' => static::getMostPopularElement($this->sessionHandler, $this->cache),
            'MOST_POPULAR_COURSES' => static::getMostPopularCourses($this->sessionHandler, $this->cache, $this->settings),
            'LATEST_VISITED' => ElementController::getLastVisitedCourses(static::PROCESSORS_LIMIT),
            'USER_PREFERENCES' => static::getUserPreferences($this->sessionHandler),
            'USER_FAVORITES' => array_reverse(static::getUserListing($this->sessionHandler, static::FAVORITES)),
            'USER_LAST_VISITED_COURSES' => static::getUserListing($this->sessionHandler, static::LATEST_COURSE_VISITED),
            'CONST' => static::getPopularConsts()
        ];
    }

    public static function fromSessionHandler(SessionHandler $sessionHandler)
    {
        return new FrontpageFetchProcessor($sessionHandler);
    }

    private static function getMostPopularElement(SessionHandler $sessionHandler, $cache)
    {
        return PopularElementsProcessor
            ::fromSessionHandler($sessionHandler)
            ->withCache($cache)
            ->run(static::PROCESSORS_LIMIT);
    }

    private static function getMostPopularCourses(SessionHandler $sessionHandler, $cache, $settings)
    {
        return PopularCoursesProcessor
            ::fromSessionHandler($sessionHandler)
            ->withCache($cache)
            ->withSettings($settings)
            ->run(static::PROCESSORS_LIMIT);
    }

    private static function getPopularConsts()
    {
        return [
            'TODAY' => MostPopularElement::TODAY,
            'WEEK' => MostPopularElement::WEEK,
            'MONTH' => MostPopularElement::MONTH,
            'YEAR' => MostPopularElement::YEAR,
            'ALL' => MostPopularElement::ALL
        ];
    }

    private static function getUserPreferences(SessionHandler $sessionHandler)
    {
        return [
            'DELTA_POST_POPULAR_ELEMENTS' => static::getUserPreferenceForKey($sessionHandler, 'most_popular_element'),
            'DELTA_POST_POPULAR_COURSES' => static::getUserPreferenceForKey($sessionHandler, 'most_popular_course'),
        ];
    }

    private static function getUserPreferenceForKey(SessionHandler $sessionHandler, $key)
    {
        $frontpageSettings = $sessionHandler->getDataWithKey('frontpage');
        if ($frontpageSettings === null or !is_array($frontpageSettings)) {
            return MostPopularElement::MONTH;
        }

        if (!isset($frontpageSettings[$key])) {
            return MostPopularElement::MONTH;
        }

        return $frontpageSettings[$key];
    }

    private static function getUserListing(SessionHandler $sessionHandler, $type = self::FAVORITES)
    {
        $ids = $sessionHandler->getDataWithKey($type);
        if (!is_array($ids) or count($ids) === 0) {
            return [];
        }

        $elements = [];
        foreach ($ids as $id) {
            $element = Element::fromId($id, ['id', 'name', 'parent', 'directory', 'checksum', 'link', 'pending', 'deleted', 'uri']);
            if ($elements !== null) {
                $elements[] = $element;
            }
        }

        return $elements;
    }
}

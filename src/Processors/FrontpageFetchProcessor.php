<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;
use Youkok\Enums\MostPopularElement;
use Youkok\Helpers\SessionHandler;
use Youkok\Helpers\Utilities;
use Youkok\Models\Download;
use Youkok\Models\Element;

class FrontpageFetchProcessor
{
    const PROCESSORS_LIMIT = 15;
    const FAVORITES = 'favorites';
    const LATEST_COURSE_VISITED = 'latest_course_visited';

    private $sessionHandler;
    private $cache;

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

    public function run()
    {
        return [
            'INFO_FILES' => Utilities::numberFormat(Element::where('directory', 0)->where('deleted', 0)->count()),
            'INFO_DOWNLOADS' => Utilities::numberFormat(Download::count()),
            'LATEST_ELEMENTS' => ElementController::getLatest(static::PROCESSORS_LIMIT),
            'MOST_POPULAR_ELEMENTS' => static::getMostPopularElement($this->sessionHandler, $this->cache),
            'MOST_POPULAR_COURSES' => static::getMostPopularCourses($this->sessionHandler, $this->cache),
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

    private static function getMostPopularCourses(SessionHandler $sessionHandler, $cache)
    {
        return PopularCoursesProcessor
            ::fromSessionHandler($sessionHandler)
            ->withCache($cache)
            ->run();
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

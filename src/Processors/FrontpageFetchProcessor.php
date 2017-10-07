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
    const FAVORITES = 'favorites';
    const DOWNLOADS = 'downloads';

    public static function fromSessionHandler(SessionHandler $sessionHandler)
    {
        return [
            'INFO_FILES' => Utilities::numberFormat(Element::where('directory', 0)->where('deleted', 0)->count()),
            'INFO_DOWNLOADS' => Utilities::numberFormat(Download::count()),
            'LATEST_ELEMENTS' => ElementController::getLatest(15),
            'MOST_POPULAR_ELEMENTS' => static::getMostPopularElementFromSessionHandler($sessionHandler),
            'MOST_POPULAR_COURSES' => static::getMostPopularCoursesFromSessionHandler($sessionHandler),
            'LATEST_VISITED' => ElementController::getLastVisitedCourses(15),
            'USER_PREFERENCES' => static::getUserPreferences($sessionHandler),
            'USER_FAVORITES' => static::getUserListing($sessionHandler, static::FAVORITES),
            'USER_DOWNLOADS' => static::getUserListing($sessionHandler, static::DOWNLOADS),
            'CONST' => static::getPopularConsts()
        ];
    }

    private static function getMostPopularElementFromSessionHandler(SessionHandler $sessionHandler)
    {
        return PopularElementsProcessor::fromSessionHandler($sessionHandler);
    }

    private static function getMostPopularCoursesFromSessionHandler(SessionHandler $sessionHandler)
    {
        return PopularCoursesProcessor::fromSessionHandler($sessionHandler);
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

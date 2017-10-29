<?php
namespace Youkok\Processors\PopularListing;

use Youkok\Enums\MostPopularElement;
use Youkok\Helpers\CacheHelper;
use Youkok\Helpers\SessionHandler;
use Youkok\Models\Element;

class PopularCoursesProcessor extends AbstractPopularListingProcessor
{
    const CACHE_DIRECTORY_KEY = 'cache_directory';
    const CACHE_DIRECTORY_SUB = 'courses';

    public static function fromDelta($delta = MostPopularElement::MONTH, $limit = null, $cache, $settings = null)
    {
        $result = CacheHelper::getMostPopularCoursesFromDelta($cache, $delta, $settings);
        if (empty($result)) {
            return [];
        }

        if ($result === null or strlen($result) === 0) {
            return [];
        }

        $resultArr = json_decode($result, true);
        if (!is_array($resultArr) or empty($resultArr)) {
            return [];
        }

        return static::resultArrayToElements($resultArr);
    }

    public static function fromSessionHandler(SessionHandler $sessionHandler)
    {
        return new PopularCoursesProcessor($sessionHandler, 'most_popular_course');
    }

    private static function resultArrayToElements(array $result)
    {
        $elements = [];
        foreach ($result as $res) {
            $element = Element::fromId($res['id'], ['id', 'name', 'slug', 'uri', 'link', 'empty', 'parent']);
            $element->_downloads = $res['downloads'];

            $elements[] = $element;
        }
        return $elements;
    }
}

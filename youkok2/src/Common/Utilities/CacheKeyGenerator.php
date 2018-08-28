<?php
namespace Youkok\Common\Utilities;

class CacheKeyGenerator
{
    public static function keyForElementDownloads($id)
    {
        return 'downloads_' . $id;
    }

    public static function keyForMostPopularElementsForDelta($delta)
    {
        return 'most_popular_elements_' . $delta;
    }

    public static function keyForMostPopularCoursesForDelta($delta)
    {
        return 'most_popular_courses_' . $delta;
    }

    public static function keyForElementUri($uri)
    {
        return 'uri_' . UriCleaner::cleanAll($uri);
    }
}

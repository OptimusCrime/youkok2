<?php
namespace Youkok\Common\Utilities;

class CacheKeyGenerator
{
    public static function keyForElementDownloads($id): string
    {
        return 'downloads_' . $id;
    }

    public static function keyForMostPopularElementsForDelta($delta): string
    {
        return 'most_popular_elements_' . $delta;
    }

    public static function keyForMostPopularCoursesForDelta($delta): string
    {
        return 'most_popular_courses_' . $delta;
    }
}

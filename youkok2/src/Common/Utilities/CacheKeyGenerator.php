<?php
namespace Youkok\Common\Utilities;

class CacheKeyGenerator
{
    public static function keyForElementDownloads(int $id): string
    {
        return 'downloads_' . $id;
    }

    public static function keyForMostPopularElementsForDelta(string $delta): string
    {
        return 'most_popular_elements_' . $delta;
    }

    public static function keyForMostPopularCoursesForDelta(string $delta): string
    {
        return 'most_popular_courses_' . $delta;
    }
}

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

    public static function keyForBoxesNumberOfFiles(): string
    {
        return 'boxes_number_of_files';
    }

    public static function keyForBoxesNumberOfDownloads(): string
    {
        return 'boxes_number_of_downloads';
    }

    public static function keyForBoxesNumberOfCoursesWithContent(): string
    {
        return 'boxes_number_courses_with_content';
    }

    public static function keyForBoxesNumberOfFilesThisMonth(): string
    {
        return 'boxes_number_new_elements';
    }
}

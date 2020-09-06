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

    public static function keyForTotalNumberOfDownloads(): string
    {
        return 'total_number_of_downloads';
    }

    public static function keyForBoxesNumberOfFiles(): string
    {
        return 'boxes_number_of_files';
    }

    public static function keyForBoxesNumberOfCoursesWithContent(): string
    {
        return 'boxes_number_courses_with_content';
    }

    public static function keyForBoxesNumberOfFilesThisMonth(): string
    {
        return 'boxes_number_new_elements';
    }

    public static function keyForNewestElementsPayload(): string
    {
        return 'payload_newest_elements';
    }

    public static function keyForLastVisitedCoursesPayload(): string
    {
        return 'payload_last_visited_courses';
    }

    public static function keyForLastDownloadedPayload(): string
    {
        return 'payload_last_downloaded';
    }

    public static function keyForVisibleUriDirectory(string $uri): string
    {
        return 'uri_visible_directory_' . $uri;
    }

    public static function keyForAllParentsAreDirectoriesExceptCurrentIsFile(string $uri): string
    {
        return 'uri_visible_parents_directory_current_file_' . $uri;
    }

    public static function keyForVisibleUriFile(string $uri): string
    {
        return 'uri_visible_file_' . $uri;
    }

    public static function keyForLastVisitedCourseSet(): string
    {
        return 'last_visited_courses_set';
    }
}

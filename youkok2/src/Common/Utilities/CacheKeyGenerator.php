<?php
namespace Youkok\Common\Utilities;

use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

class CacheKeyGenerator
{
    public static function keyForMostPopularElementsSetForDelta(MostPopularElement $delta): string
    {
        return 'most_popular_elements_set_' . $delta->getValue();
    }

    public static function keyForMostPopularElementsForDelta(MostPopularElement $delta): string
    {
        return 'most_popular_elements_' . $delta->getValue();
    }

    public static function keyForMostPopularCoursesForDelta(MostPopularCourse $delta): string
    {
        return 'most_popular_courses_' . $delta->getValue();
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

    public static function keyForElementParent(int $id): string
    {
        return 'element_parent_' . $id;
    }

    public static function keyForAllParentsAreDirectoriesExceptCurrentIsFile(string $uri): string
    {
        return 'uri_visible_parents_directory_current_file_' . $uri;
    }

    public static function keyForVisibleUriFile(string $uri): string
    {
        return 'uri_visible_file_' . $uri;
    }

    public static function keyForCoursesLookupChecksum(): string
    {
        return 'courses_lookup_checksum';
    }

    public static function keyForCoursesLookupData(): string
    {
        return 'courses_lookup_data';
    }
}

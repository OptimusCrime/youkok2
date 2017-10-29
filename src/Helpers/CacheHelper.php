<?php
namespace Youkok\Helpers;

use Youkok\CachePopulators\PopulateMostPopularCourses;
use Youkok\CachePopulators\PopulateMostPopularElements;
use Youkok\Processors\FrontpageFetchProcessor;
use Youkok\Processors\PopularListing\PopularCoursesProcessor;
use Youkok\Utilities\CacheKeyGenerator;

class CacheHelper
{
    public static function getMostPopularElementsFromDelta($cache, $delta, $limit)
    {
        $key = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);

        $result = static::getSortedRangeByKey($cache, $key, $limit);
        if (!empty($result)) {
            return $result;
        }

        PopulateMostPopularElements
            ::setCache($cache)
            ->withDelta($delta)
            ->run();

        // Attempt to fetch the result again
        return static::getSortedRangeByKey($cache, $key, $limit);
    }

    public static function getMostPopularCoursesFromDelta($cache, $delta, $settings)
    {
        $key = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);
        $result = static::getCacheByKey($cache, $key);

        if (empty($result)) {
            return static::getMostPopularCoursesFromDisk($key, $settings);
        }

        return $result;
    }

    private static function getMostPopularCoursesFromDisk($key, $settings)
    {
        $cacheDirectory = $settings[PopularCoursesProcessor::CACHE_DIRECTORY_KEY]
            . PopularCoursesProcessor::CACHE_DIRECTORY_SUB;
        $cacheFile = $cacheDirectory . DIRECTORY_SEPARATOR . $key . '.json';

        $fileContents = file_get_contents($cacheFile);
        if ($fileContents === null or strlen($fileContents) === 0) {
            return [];
        }

        // We return the entire JSON string here.
        return $fileContents;
    }

    private static function getSortedRangeByKey($cache, $key, $limit, $offset = 0)
    {
        if ($cache === null) {
            return [];
        }

        return $cache->zRevRangeByScore($key, '+inf', '-inf', [
            'limit' => [
                $offset,
                $limit
            ],
            'withscores' => true
        ]);
    }

    private static function getCacheByKey($cache, $key)
    {
        if ($cache === null) {
            return [];
        }

        return $cache->get($key);
    }
}

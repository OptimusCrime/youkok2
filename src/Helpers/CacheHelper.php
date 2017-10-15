<?php
namespace Youkok\Helpers;

use Youkok\CachePopulators\PopulateMostPopularCourses;
use Youkok\CachePopulators\PopulateMostPopularElements;
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

    public static function getMostPopularCoursesFromDelta($cache, $delta)
    {
        $key = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);
        $result = static::getCacheByKey($cache, $key);

        if (empty($result)) {
            // Attempt to fetch on disk
        }

        return $result;
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
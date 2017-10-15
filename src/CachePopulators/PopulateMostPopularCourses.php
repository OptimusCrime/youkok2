<?php
namespace Youkok\CachePopulators;

use Youkok\Controllers\DownloadController;
use Youkok\Utilities\CacheKeyGenerator;

class PopulateMostPopularCourses extends AbstractCachePopulator
{
    private $delta;
    private $config;

    public static function setCache($cache)
    {
        return new PopulateMostPopularCourses($cache);
    }

    public function withDelta($delta)
    {
        $this->delta = $delta;
        return $this;
    }

    public function withConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    public function run()
    {
        $courses = DownloadController::getMostPopularCoursesFromDelta($this->delta);
        if (count($courses) === 0) {
            return false;
        }

        $setKey = CacheKeyGenerator::keyForMostPopularCoursesForDelta($this->delta);

        static::insertCoursesToCache($this->cache, $setKey, $courses);
        static::storeDataInFile($this->config, $setKey, $courses);
    }

    private static function insertCoursesToCache($cache, $setKey, $courses)
    {
        foreach ($courses as $course) {
            $cache->zadd($setKey, $course->download_count, $course->id);
        }
    }

    private static function storeDataInFile($config, $setKey, $courses)
    {
        // TODO
    }
}
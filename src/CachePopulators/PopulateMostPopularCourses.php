<?php
namespace Youkok\CachePopulators;

use Youkok\Controllers\DownloadController;
use Youkok\Processors\PopularListing\PopularCoursesProcessor;
use Youkok\Utilities\CacheKeyGenerator;

class PopulateMostPopularCourses extends AbstractCachePopulator
{
    private $delta;
    private $config;
    private $limit;

    public static function setCache($cache)
    {
        return new PopulateMostPopularCourses($cache);
    }

    public function withDelta($delta)
    {
        $this->delta = $delta;
        return $this;
    }

    public function withLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function withConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    public function run()
    {
        $courses = DownloadController::getMostPopularCoursesFromDelta($this->delta, $this->limit);
        $setKey = CacheKeyGenerator::keyForMostPopularCoursesForDelta($this->delta);

        static::insertCoursesToCache($this->cache, $setKey, $courses);
        static::storeDataInFile($this->config, $setKey, $courses);
    }

    private static function insertCoursesToCache($cache, $setKey, $courses)
    {
        $cache->set($setKey, json_encode($courses));
    }

    private static function storeDataInFile($config, $setKey, $courses)
    {
        // Make sure we have the directory first
        if (!static::createCacheDirectory($config)) {
            return false;
        }

        $cacheDirectory = static::getCacheDirectory($config);
        return file_put_contents($cacheDirectory . DIRECTORY_SEPARATOR . $setKey . '.json', json_encode($courses));
    }

    private static function createCacheDirectory($config)
    {
        $cacheDirectory = static::getCacheDirectory($config);
        if (file_exists($cacheDirectory)) {
            return true;
        }

        return mkdir($cacheDirectory, 0777, true);
    }

    private static function getCacheDirectory($config)
    {
        return $config[PopularCoursesProcessor::CACHE_DIRECTORY_KEY] . PopularCoursesProcessor::CACHE_DIRECTORY_SUB;
    }
}
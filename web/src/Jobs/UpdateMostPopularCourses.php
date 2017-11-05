<?php
namespace Youkok\Jobs;

use Youkok\CachePopulators\PopulateMostPopularCourses;
use Youkok\Enums\MostPopularCourse;
use Youkok\Processors\FrontpageFetchProcessor;
use Youkok\Processors\PopularListing\PopularCoursesProcessor;
use Youkok\Utilities\CacheKeyGenerator;

class UpdateMostPopularCourses extends JobInterface
{
    private static $mostPopularKeys = [
        MostPopularCourse::TODAY,
        MostPopularCourse::WEEK,
        MostPopularCourse::MONTH,
        MostPopularCourse::YEAR,
        MostPopularCourse::ALL,
    ];

    public function run()
    {
        $this->clearCache();
        $this->populateCache();
    }

    private function clearCache()
    {
        $this->clearInMemoryCache();
        $this->clearFileCache();
    }

    private function clearInMemoryCache()
    {
        $cache = $this->containers->get('cache');
        if ($cache === null) {
            return null;
        }

        foreach (static::$mostPopularKeys as $key) {
            $cacheKey = CacheKeyGenerator::keyForMostPopularCoursesForDelta($key);
            $cache->delete($cacheKey);
        }
    }

    private function clearFileCache()
    {
        $cacheDirectory = $this->containers->get('settings')[PopularCoursesProcessor::CACHE_DIRECTORY_KEY]
            . PopularCoursesProcessor::CACHE_DIRECTORY_SUB;
        if (!file_exists($cacheDirectory)) {
            return;
        }

        $files = glob($cacheDirectory . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            unlink($file);
        }

        return true;
    }

    private function populateCache()
    {
        $cache = $this->containers->get('cache');
        if ($cache === null) {
            return null;
        }

        foreach (static::$mostPopularKeys as $key) {
            PopulateMostPopularCourses
                ::setCache($cache)
                ->withDelta($key)
                ->withLimit(FrontpageFetchProcessor::PROCESSORS_LIMIT)
                ->withConfig($this->containers->get('settings'))
                ->run();
        }
    }
}

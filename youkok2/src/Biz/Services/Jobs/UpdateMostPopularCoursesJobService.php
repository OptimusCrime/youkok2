<?php
namespace Youkok\Biz\Services\Jobs;

use Redis;

use Youkok\CachePopulators\PopulateMostPopularCourses;
use Youkok\Enums\MostPopularCourse;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\PopularListing\PopularCoursesService;
use Youkok\Common\Utilities\CacheKeyGenerator;

class UpdateMostPopularCoursesJobService implements JobServiceInterface
{
    /** @var \Redis */
    private $cache;

    /** @var \Youkok\CachePopulators\PopulateMostPopularElements */
    private $populateMostPopularElements;

    public function __construct(Redis $cache)
    {
        $this->cache = $cache;
        $this->populateMostPopularElements = $container->get(PopulateMostPopularElements::class);
    }

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
        $cache = $this->cache;
        if ($cache === null) {
            return null;
        }

        foreach (MostPopularCourse::all() as $key) {
            $cacheKey = CacheKeyGenerator::keyForMostPopularCoursesForDelta($key);
            $cache->delete($cacheKey);
        }
    }

    private function clearFileCache()
    {
        $cacheDirectory = $this->containers->get('settings')[PopularCoursesService::CACHE_DIRECTORY_KEY]
            . PopularCoursesService::CACHE_DIRECTORY_SUB;
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
        $cache = $this->cache;
        if ($cache === null) {
            return null;
        }

        foreach (static::$mostPopularKeys as $key) {
            PopulateMostPopularCourses
                ::setCache($cache)
                ->withDelta($key)
                ->withLimit(FrontpageService::PROCESSORS_LIMIT)
                ->withConfig($this->containers->get('settings'))
                ->run();
        }
    }
}

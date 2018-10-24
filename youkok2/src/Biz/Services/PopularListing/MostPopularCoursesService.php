<?php
namespace Youkok\Biz\Services\PopularListing;

use Youkok\Biz\Services\CacheService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Enums\MostPopularCourse;

class MostPopularCoursesService implements MostPopularInterface
{
    const CACHE_DIRECTORY_KEY = 'cache_directory';
    const CACHE_DIRECTORY_SUB = 'courses';

    private $settings;
    private $cacheService;

    public function __construct(array $settings, CacheService $cacheService) {
        $this->settings = $settings;
        $this->cacheService = $cacheService;
    }

    public function refresh()
    {
        $this->clearFileCache();
        $this->cacheService->clearCacheForKeys(MostPopularCourse::all());
    }

    public function fromDelta($delta, $limit = null)
    {
        $result = $this->cacheService->getMostPopularCoursesFromDelta($delta);
        if (empty($result)) {
            // We did not find the set in the cache, try to load it from disk
            $result = $this->getMostPopularCoursesFromDisk($delta);
        }

        if ($result === null or strlen($result) === 0) {
            return [];
        }

        $resultArr = json_decode($result, true);
        if (!is_array($resultArr) or empty($resultArr)) {
            return [];
        }

        return static::resultArrayToElements($resultArr, $limit);
    }

    private function getMostPopularCoursesFromDisk($delta)
    {
        $key = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);

        $cacheDirectoryKey = $this->settings[MostPopularCoursesService::CACHE_DIRECTORY_KEY];
        $cacheDirectory = $cacheDirectoryKey . MostPopularCoursesService::CACHE_DIRECTORY_SUB;
        $cacheFile = $cacheDirectory . DIRECTORY_SEPARATOR . $key . '.json';

        return file_get_contents($cacheFile);
    }

    private function clearFileCache()
    {
        $cacheDirectoryKey = $this->settings[MostPopularCoursesService::CACHE_DIRECTORY_KEY];
        $cacheDirectory = $cacheDirectoryKey . MostPopularCoursesService::CACHE_DIRECTORY_SUB;
        if (!file_exists($cacheDirectory)) {
            return null;
        }

        $files = glob($cacheDirectory . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    private static function resultArrayToElements(array $result, $limit = null)
    {
        $elements = [];
        foreach ($result as $res) {
            $element = Element::fromIdVisible($res['id'], ['id', 'name', 'slug', 'uri', 'link', 'empty', 'parent']);
            $element->_downloads = $res['downloads'];

            $elements[] = $element;
        }

        if ($limit === null) {
            return $elements;
        }

        return static::resultArrayToMaxLimit($elements, $limit);
    }

    private static function resultArrayToMaxLimit(array $elements, $limit)
    {
        $newElements = [];
        foreach ($elements as $element) {
            $newElements[] = $element;
            if (count($newElements) === $limit) {
                break;
            }
        }

        return $newElements;
    }
}

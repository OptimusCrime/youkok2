<?php
namespace Youkok\Biz\Services\PopularListing;

use Slim\Collection;

use Youkok\Biz\Services\CacheService;
use Youkok\Common\Controllers\DownloadController;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Enums\MostPopularCourse;

class MostPopularCoursesService implements MostPopularInterface
{
    // We only display 10 on the actual frontpage, but it does not really make any differece. Good to have?
    const MAX_COURSES_TO_FETCH = 20;

    const CACHE_DIRECTORY_KEY = 'CACHE_DIRECTORY';
    const CACHE_DIRECTORY_SUB = 'courses';

    private $settings;
    private $cacheService;

    public function __construct(Collection $settings, CacheService $cacheService) {
        $this->settings = $settings;
        $this->cacheService = $cacheService;
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

    public function refresh()
    {
        $this->clearFileCache();
        $this->cacheService->clearCacheForKeys(MostPopularCourse::all());

        foreach (MostPopularCourse::all() as $key) {
            $this->refreshForDelta($key);
        }
    }

    private function refreshForDelta($delta)
    {
        $courses = DownloadController::getMostPopularCoursesFromDelta($delta, static::MAX_COURSES_TO_FETCH);
        $setKey = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);

        $this->cacheService->setByKey($setKey, json_encode($courses));

        $this->storeDataInFile($setKey, $courses);
    }

    private function storeDataInFile($setKey, $courses)
    {
        // Make sure we have the directory first
        if (!$this->createCacheDirectory()) {
            // TODO error log here
            return false;
        }

        $cacheDirectory = static::getCacheDirectory();
        return file_put_contents($cacheDirectory . DIRECTORY_SEPARATOR . $setKey . '.json', json_encode($courses));
    }

    private function createCacheDirectory()
    {
        $cacheDirectory = $this->getCacheDirectory();
        if (file_exists($cacheDirectory)) {
            return true;
        }

        return mkdir($cacheDirectory, 0777, true);
    }

    private function getMostPopularCoursesFromDisk($delta)
    {
        $key = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);

        $cacheDirectory = $this->getCacheDirectory();
        $cacheFile = $cacheDirectory . DIRECTORY_SEPARATOR . $key . '.json';

        return file_get_contents($cacheFile);
    }

    private function clearFileCache()
    {
        $cacheDirectory = $this->getCacheDirectory();
        if (!file_exists($cacheDirectory)) {
            return null;
        }

        $files = glob($cacheDirectory . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    private function getCacheDirectory()
    {
        $cacheDirectory = getenv(static::CACHE_DIRECTORY_KEY);
        return $cacheDirectory . static::CACHE_DIRECTORY_SUB;
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
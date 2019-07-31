<?php

namespace Youkok\Biz\Services\PopularListing;

use Monolog\Logger;
use Slim\Collection;

use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Enums\MostPopularCourse;

class MostPopularCoursesService implements MostPopularInterface
{
    // We only display 10 on the actual frontpage, but it does not really make any difference. Good to have?
    const MAX_COURSES_TO_FETCH = 20;

    const CACHE_DIRECTORY_KEY = 'CACHE_DIRECTORY';
    const CACHE_DIRECTORY_SUB = 'courses';

    private $cacheService;
    private $logger;
    private $downloadService;
    private $elementService;

    public function __construct(
        CacheService $cacheService,
        Logger $logger,
        DownloadService $downloadService,
        ElementService $elementService
    ) {
        $this->cacheService = $cacheService;
        $this->logger = $logger;
        $this->downloadService = $downloadService;
        $this->elementService = $elementService;
    }

    public function fromDelta(string $delta, int $limit): array
    {
        $result = $this->cacheService->getMostPopularCoursesFromDelta($delta);
        if (empty($result)) {
            // We did not find the set in the cache, try to load it from disk
            $result = $this->getMostPopularCoursesFromDisk($delta);
        }

        if ($result === null or mb_strlen($result) === 0) {
            return [];
        }

        $resultArr = json_decode($result, true);
        if (!is_array($resultArr) or empty($resultArr)) {
            return [];
        }

        return $this->resultArrayToElements($resultArr, $limit);
    }

    public function refresh(): void
    {
        $this->clearFileCache();
        $this->cacheService->clearCacheForKeys(MostPopularCourse::all());

        foreach (MostPopularCourse::all() as $key) {
            $this->refreshForDelta($key);
        }
    }

    private function refreshForDelta(string $delta): void
    {
        $courses = $this->downloadService->getMostPopularCoursesFromDelta($delta, static::MAX_COURSES_TO_FETCH);
        $setKey = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);

        $this->cacheService->set($setKey, json_encode($courses));

        $this->storeDataInFile($setKey, $courses);
    }

    private function storeDataInFile(string $setKey, array $courses): bool
    {
        // Make sure we have the directory first
        if (!$this->createCacheDirectory()) {
            $this->logger->error('Failed to create cache directory');

            return false;
        }

        $cacheDirectory = static::getCacheDirectory();

        $write = file_put_contents($cacheDirectory . DIRECTORY_SEPARATOR . $setKey . '.json', json_encode($courses));

        // file_put_contents returns false on error, otherwise the number of bytes written
        if ($write !== false) {
            return true;
        }

        return false;
    }

    private function createCacheDirectory(): bool
    {
        $cacheDirectory = $this->getCacheDirectory();
        if (file_exists($cacheDirectory)) {
            return true;
        }

        return mkdir($cacheDirectory, 0777, true);
    }

    private function getMostPopularCoursesFromDisk(string $delta): ?string
    {
        $key = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);

        $cacheDirectory = $this->getCacheDirectory();
        $cacheFile = $cacheDirectory . DIRECTORY_SEPARATOR . $key . '.json';

        return @file_get_contents($cacheFile);
    }

    private function clearFileCache(): void
    {
        $cacheDirectory = $this->getCacheDirectory();
        if (!file_exists($cacheDirectory)) {
            return;
        }

        $files = glob($cacheDirectory . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    private function getCacheDirectory(): string
    {
        $cacheDirectory = getenv(static::CACHE_DIRECTORY_KEY);
        return $cacheDirectory . static::CACHE_DIRECTORY_SUB;
    }

    private function resultArrayToElements(array $result, int $limit): array
    {
        $elements = [];
        foreach ($result as $res) {
            $element = $this->elementService->getElement(
                new SelectStatements('id', $res['id']),
                ['id', 'name', 'slug', 'uri', 'link', 'empty', 'parent'],
                [
                    ElementService::FLAG_ENSURE_VISIBLE
                ]
            );
            $element->setDownloads($res['downloads']);

            $elements[] = $element;
        }

        if ($limit === null) {
            return $elements;
        }

        return static::resultArrayToMaxLimit($elements, $limit);
    }

    private static function resultArrayToMaxLimit(array $elements, int $limit): array
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

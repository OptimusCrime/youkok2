<?php

namespace Youkok\Biz\Services\Download;

use RedisException;
use Youkok\Biz\Exceptions\InvalidValueException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Enums\MostPopularElement;

class DownloadCacheSetService
{
    private CacheService $cacheService;
    private DownloadService $downloadService;

    public function __construct(
        CacheService    $cacheService,
        DownloadService $downloadService,
    )
    {
        $this->cacheService = $cacheService;
        $this->downloadService = $downloadService;
    }

    /**
     * @throws InvalidValueException
     * @throws RedisException
     */
    public function createMostPopularElementsForDeltaCacheIfNecessary(MostPopularElement $delta): void
    {
        // Dumb alias...
        $this->getMostPopularElementsForDeltaFromCacheSetOrCreatIfNecessary($delta);
    }

    /**
     * @throws RedisException
     * @throws InvalidValueException
     */
    public function getMostPopularElementsForDeltaFromCacheSetOrCreatIfNecessary(MostPopularElement $delta): array
    {
        $mostPopularSet = $this->cacheService->getMostPopularElementsSetFromDelta($delta);
        if (count($mostPopularSet) > 0) {
            return $mostPopularSet;
        }

        $this->createMostPopularCacheSetForDelta($delta);
        return $this->cacheService->getMostPopularElementsSetFromDelta($delta);
    }

    /**
     * @throws RedisException
     * @throws InvalidValueException
     */
    public function createMostPopularCacheSetForDelta(MostPopularElement $delta): void
    {
        $elements = $this->downloadService->getMostPopularElementsFromDelta($delta);
        $key = CacheKeyGenerator::keyForMostPopularElementsSetForDelta($delta);

        foreach ($elements as $element) {
            $downloads = static::getDownloadsFromElement($element, $delta);
            $this->cacheService->insertIntoSet($key, $downloads, (string)$element->id);
        }
    }

    /**
     * @throws InvalidValueException
     */
    private static function getDownloadsFromElement(Element $element, MostPopularElement $delta): int
    {
        switch ($delta->getValue()) {
            case MostPopularElement::DAY()->getValue():
                return $element->downloads_today;
            case MostPopularElement::WEEK()->getValue():
                return $element->downloads_week;
            case MostPopularElement::MONTH()->getValue():
                return $element->downloads_month;
            case MostPopularElement::YEAR()->getValue():
                return $element->downloads_year;
            case MostPopularElement::ALL()->getValue():
                return $element->downloads_all;
            default:
                throw new InvalidValueException('Unexpected most popular element value: ' . $delta->getValue());
        }
    }
}

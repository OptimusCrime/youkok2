<?php

namespace Youkok\Biz\Services\PopularListing;

use Exception;
use Monolog\Logger;

use RedisException;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Enums\MostPopularElement;

class MostPopularElementsService
{
    private CacheService $cacheService;
    private DownloadService $downloadService;
    private ElementService $elementService;
    private ElementMapper $elementMapper;
    private Logger $logger;

    public function __construct(
        CacheService    $cacheService,
        DownloadService $downloadService,
        ElementService  $elementService,
        ElementMapper   $elementMapper,
        Logger          $logger
    )
    {
        $this->cacheService = $cacheService;
        $this->downloadService = $downloadService;
        $this->elementService = $elementService;
        $this->elementMapper = $elementMapper;
        $this->logger = $logger;
    }

    /**
     * @throws RedisException
     * @throws Exception
     */
    public function getMostPopularElements(RouteParserInterface $routeParser, MostPopularElement $delta, int $limit): array
    {
        $key = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);
        $cache = $this->cacheService->get($key);
        if ($cache !== null) {
            return json_decode($cache, true);
        }

        $mostPopularSet = $this->cacheService->getMostPopularElementsSetFromDelta($delta);
        if (count($mostPopularSet) === 0) {
            $this->buildMostPopularCacheSet($delta);
            $mostPopularSet = $this->cacheService->getMostPopularElementsSetFromDelta($delta);
        }

        $elements = [];
        foreach ($mostPopularSet as $id => $downloads) {
            try {
                $element = $this->elementService->getElement(
                    new SelectStatements('id', $id),
                    [
                        ElementService::FLAG_ENSURE_VISIBLE
                    ]
                );

                $elements[] = $element;

                if (count($elements) === $limit) {
                    break;
                }
            } catch (ElementNotFoundException $ex) {
                $this->logger->debug('Could not find element ' . $id);
                continue;
            }
        }

        $response = $this->mapMostPopularElements($routeParser, $elements, $delta, $limit);
        $this->cacheService->set($key, json_encode($response));

        return $response;
    }

    /**
     * @throws RedisException
     */
    private function mapMostPopularElements(RouteParserInterface $routeParser, array $elements, MostPopularElement $delta, int $limit): array
    {
        $fields = [
            ElementMapper::PARENT_DIRECT,
            ElementMapper::PARENT_COURSE
        ];

        switch ($delta->getValue()) {
            case MostPopularElement::DAY()->getValue():
                $fields[] = ElementMapper::DOWNLOADS_TODAY;
                break;
            case MostPopularElement::WEEK()->getValue():
                $fields[] = ElementMapper::DOWNLOADS_WEEK;
                break;
            case MostPopularElement::MONTH()->getValue():
                $fields[] = ElementMapper::DOWNLOADS_MONTH;
                break;
            case MostPopularElement::YEAR()->getValue():
                $fields[] = ElementMapper::DOWNLOADS_YEAR;
                break;
            case MostPopularElement::ALL()->getValue():
            default:
                $fields[] = ElementMapper::DOWNLOADS_ALL;
                break;

        }

        $response = [];
        foreach ($elements as $element) {
            try {
                $element = $this->elementMapper->mapElement(
                    $routeParser,
                    $element,
                    $fields
                );

                $response[] = $element;

                if (count($response) === $limit) {
                    break;
                }
            } catch (ElementNotFoundException $ex) {
                $this->logger->debug($ex);
                continue;
            }
        }

        return $response;
    }

    /**
     * @throws RedisException
     * @throws Exception
     */
    private function buildMostPopularCacheSet(MostPopularElement $delta): void
    {
        $elements = $this->downloadService->getMostPopularElementsFromDelta($delta);
        $key = CacheKeyGenerator::keyForMostPopularElementsSetForDelta($delta);

        foreach ($elements as $element) {
            $downloads = static::getDownloadsFromElement($element, $delta);
            $this->cacheService->insertIntoSet($key, (int)$downloads, (string)$element->id);
        }
    }

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
            default:
                return $element->downloads_all;
        }
    }
}

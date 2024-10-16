<?php

namespace Youkok\Biz\Services\PopularListing;

use Exception;
use Monolog\Logger;

use RedisException;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\InvalidValueException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Download\DownloadCacheSetService;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Enums\MostPopularElement;

class MostPopularElementsService
{
    private CacheService $cacheService;
    private ElementService $elementService;
    private ElementMapper $elementMapper;
    private DownloadCacheSetService $downloadCacheSetService;
    private Logger $logger;

    public function __construct(
        CacheService    $cacheService,
        ElementService  $elementService,
        ElementMapper   $elementMapper,
        DownloadCacheSetService $downloadCacheSetService,
        Logger          $logger
    )
    {
        $this->cacheService = $cacheService;
        $this->elementService = $elementService;
        $this->elementMapper = $elementMapper;
        $this->downloadCacheSetService = $downloadCacheSetService;
        $this->logger = $logger;
    }

    /**
     * @throws RedisException
     * @throws InvalidValueException
     * @throws Exception
     */
    public function getMostPopularElements(RouteParserInterface $routeParser, MostPopularElement $delta, int $limit): array
    {
        $key = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);
        $cache = $this->cacheService->get($key);
        if ($cache !== null) {
            return json_decode($cache, true);
        }

        $mostPopularSet = $this->downloadCacheSetService->getMostPopularElementsForDeltaFromCacheSetOrCreatIfNecessary($delta);

        $elements = [];
        foreach ($mostPopularSet as $id => $downloads) {
            if ((int)$downloads === 0) {
                break;
            }

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
}

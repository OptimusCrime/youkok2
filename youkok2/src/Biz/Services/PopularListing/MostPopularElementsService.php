<?php
namespace Youkok\Biz\Services\PopularListing;

use Monolog\Logger;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InvalidFlagCombination;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Enums\MostPopularElement;

class MostPopularElementsService implements MostPopularInterface
{
    const MAX_ELEMENTS_TO_FETCH = 30;
    const MAX_ELEMENTS_TO_RETURN = 10;

    private CacheService $cacheService;
    private DownloadService $downloadService;
    private ElementService $elementService;
    private Logger $logger;

    public function __construct(
        CacheService $cacheService,
        DownloadService $downloadService,
        ElementService $elementService,
        Logger $logger
    ) {
        $this->cacheService = $cacheService;
        $this->downloadService = $downloadService;
        $this->elementService = $elementService;
        $this->logger = $logger;
    }

    /**
     * @param string $delta
     * @param int $limit
     * @return array
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
     */
    public function fromDelta(string $delta, int $limit): array
    {
        $ids = $this->cacheService->getMostPopularElementsFromDelta($delta, $limit);

        if (count($ids) > 0) {
            return static::idListToElements($ids);
        }

        return static::idListToElements($this->refreshForDelta($delta));
    }

    /**
     * @throws GenericYoukokException
     */
    public function refreshAll(): void
    {
        foreach (MostPopularElement::collection() as $delta) {
            $this->refresh($delta->getValue());
        }
    }

    /**
     * @param string $delta
     * @return string
     * @throws GenericYoukokException
     */
    public function refresh(string $delta): string
    {
        $cacheKey = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);
        $this->cacheService->delete($cacheKey);

        return json_encode($this->refreshForDelta($delta));
    }

    /**
     * @param string $delta
     * @return array
     * @throws GenericYoukokException
     */
    private function refreshForDelta(string $delta): array
    {
        $elements = $this->downloadService->getMostPopularElementsFromDelta($delta, static::MAX_ELEMENTS_TO_FETCH);
        $setKey = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);

        $output = [];
        $count = 0;
        foreach ($elements as $element) {
            if ($element->deleted === 1 || $element->pending === 1) {
                continue;
            }

            $this->cacheService->insertIntoSet($setKey, (int) $element->download_count, (string) $element->id);

            if ($count <= static::MAX_ELEMENTS_TO_RETURN) {
                $output[(int) $element->id] = (float) $element->download_count;
                $count++;
            }
        }

        return $output;
    }

    /**
     * @param array $ids
     * @return array
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
     */
    private function idListToElements(array $ids): array
    {
        $elements = [];
        foreach ($ids as $id => $downloads) {
            try {
                $element = $this->elementService->getElement(
                    new SelectStatements('id', $id),
                    ['id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'checksum', 'directory'],
                    [
                        ElementService::FLAG_ENSURE_VISIBLE
                    ]
                );

                $element->setDownloads($downloads);

                $elements[] = $element;
            } catch (ElementNotFoundException $ex) {
                $this->logger->warning($ex);
            }
        }
        return $elements;
    }
}

<?php
namespace Youkok\Biz\Services\Download;

use RedisException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Common\Models\Element;

class DownloadCountService
{
    private CacheService $cacheService;
    private DownloadService $downloadService;

    public function __construct(CacheService $cacheService, DownloadService $downloadService)
    {
        $this->cacheService = $cacheService;
        $this->downloadService = $downloadService;
    }

    /**
     * @throws RedisException
     */
    public function getDownloadsForElement(Element $element): int
    {
        if ($element->getType() === Element::DIRECTORY) {
            return 0;
        }

        $downloads = $this->cacheService->getDownloadsForId($element->id);

        // Redis returns false for values that does not exist for some reason
        if ($downloads !== null) {
            return $downloads;
        }

        $downloads = $this->downloadService->getDownloadsForId($element->id);

        $this->cacheService->setDownloadsForId($element->id, $downloads);

        return $downloads;
    }
}

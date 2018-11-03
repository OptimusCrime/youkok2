<?php
namespace Youkok\Biz\Services\Download;

use Youkok\Biz\Services\CacheService;
use Youkok\Common\Controllers\DownloadController;
use Youkok\Common\Models\Element;

class DownloadCountService
{
    private $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function getDownloadsForElement(Element $element)
    {
        if ($element->getType() === Element::DIRECTORY) {
            return 0;
        }

        $downloads = $this->cacheService->getDownloadsForId($element->id);

        // Redis returns false for values that does not exist for some reason
        if ($downloads !== null && $downloads !== false) {
            return (int) $downloads;
        }

        $downloads = (int) DownloadController::getDownloadsForId($element->id);

        $this->cacheService->setDownloadsForId($element->id, $downloads);

        return $downloads;
    }
}
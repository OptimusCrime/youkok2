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

        if ($downloads !== null) {
            return (int) $downloads;
        }

        return (int) DownloadController::getDownloadsForId($element->id);
    }
}
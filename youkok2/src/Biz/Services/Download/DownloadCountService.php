<?php
namespace Youkok\Biz\Services\Download;

use Illuminate\Support\Facades\DB;

use Youkok\Biz\Services\Cache\CacheService;
use Youkok\Common\Models\Download;
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

        $downloads = $this->cacheService->getDonwloadsForId($element->id);

        if ($downloads !== null) {
            return (int) $downloads;
        }

        return $this->getDownloadsFromDatabase($element);
    }

    private function getDownloadsFromDatabase(Element $element)
    {
        $downloads = Download::select(DB::raw("COUNT(`id`) as `result`"))
            ->where('resource', $element->id)
            ->count();

        $this->cacheService->setDownloadsForId($element->id, $downloads);

        return (int) $downloads;
    }
}
<?php
namespace Youkok\Biz\Services\Download;

use Redis;

use Youkok\Biz\Services\Cache\UpdateMostPopularElementRedisService;
use Youkok\Biz\Services\SessionService;
use Youkok\Common\Controllers\ElementController;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;

class UpdateDownloadsService
{
    private $sessionService;
    private $cache;
    private $updateMostPopularElementRedisProcessor;

    /** @var \Youkok\Common\Models\Element */
    private $element;

    public function __construct(
        SessionService $sessionService,
        Redis $cache,
        UpdateMostPopularElementRedisService $updateMostPopularElementRedisProcessor
    ) {
        $this->sessionService = $sessionService;
        $this->cache = $cache;
        $this->updateMostPopularElementRedisProcessor = $updateMostPopularElementRedisProcessor;
    }

    public function run(Element $element)
    {
        $this->element = $element;

        $this->addDownload();
        $this->updateMostPopularElementRedisProcessor->run($this->element);
    }

    private function addDownload()
    {
        $this->element->addDownload();

        if ($this->cache === null) {
            return;
        }

        $key = CacheKeyGenerator::keyForElementDownloads($this->element->id);
        $downloads = $this->cache->get($key);
        if ($downloads === false) {
            $existingDownloads = ElementController::getDownloadsForElement($this->element);
            if ($existingDownloads === null) {
                $existingDownloads = 0;
            }
            $this->cache->set($key, $existingDownloads);
            return;
        }

        $this->cache->incr($key);
    }
}

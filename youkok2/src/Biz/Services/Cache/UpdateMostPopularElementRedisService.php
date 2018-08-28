<?php
namespace Youkok\Biz\Services\Cache;

use Redis;

use Youkok\Enums\MostPopularElement;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;

class UpdateMostPopularElementRedisService
{
    private $cache;
    private $element;

    public function __construct(Redis $cache)
    {
        $this->cache = $cache;
    }

    public function run(Element $element)
    {
        if ($this->cache === null) {
            return;
        }

        $this->element = $element;

        foreach (MostPopularElement::all() as $delta) {
            $this->addDownloadToSet($delta);
        }
    }

    private function addDownloadToSet($delta)
    {
        $setKey = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);
        return $this->cache->zIncrBy($setKey, 1, $this->element->id);
    }
}
<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;
use Youkok\Enums\MostPopularElement;
use Youkok\Helpers\SessionHandler;
use Youkok\Models\Element;
use Youkok\Utilities\ArrayHelper;
use Youkok\Utilities\CacheKeyGenerator;

class UpdateMostPopularElementRedisProcessor
{
    private static $mostPopularDeltas = [
        MostPopularElement::TODAY,
        MostPopularElement::WEEK,
        MostPopularElement::MONTH,
        MostPopularElement::YEAR,
        MostPopularElement::ALL,
    ];

    private $element;
    private $cache;

    private function __construct(Element $element)
    {
        $this->element = $element;
    }

    public static function addElement(Element $element)
    {
        return new UpdateMostPopularElementRedisProcessor($element);
    }

    public function withCache($cache)
    {
        $this->cache = $cache;
        return $this;
    }

    public function run()
    {
        if ($this->cache === null) {
            return;
        }

        foreach (static::$mostPopularDeltas as $delta) {
            static::addDownloadToSet($this->element, $this->cache, $delta);
        }
    }

    private static function addDownloadToSet(Element $element, $cache, $delta)
    {
        $setKey = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);
        return $cache->zIncrBy($setKey, 1, $element->id);
    }
}
<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;
use Youkok\Helpers\SessionHandler;
use Youkok\Models\Element;
use Youkok\Utilities\ArrayHelper;
use Youkok\Utilities\CacheKeyGenerator;

class UpdateMostPopularElementRedisProcessor
{
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
    }

    public function run()
    {
        if ($this->cache === null) {
            return;
        }

        if (!static::setExists($this->cache)) {
            static::createSet($this->cache);
        }
    }

    private static function setExists($cache)
    {
        $key =

        var_dump($cache->get($key));
        die();
    }
}
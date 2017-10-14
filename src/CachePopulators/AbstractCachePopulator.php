<?php
namespace Youkok\CachePopulators;

abstract class AbstractCachePopulator
{
    protected $cache;

    protected function __construct($cache)
    {
        $this->cache = $cache;
    }

    abstract public function run();
}
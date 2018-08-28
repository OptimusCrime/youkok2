<?php
namespace Youkok\Biz\Services\PopularListing;

use Redis;

use Youkok\Biz\Services\Cache\CacheService;
use Youkok\Biz\Services\SessionService;

abstract class AbstractPopularListingProcessor
{
    protected $sessionHandler;
    protected $cache;
    protected $cacheService;

    private $key;

    public function __construct(
        SessionService $sessionHandler,
        Redis $cache,
        CacheService $cacheService
    ) {
        $this->sessionHandler = $sessionHandler;
        $this->cache = $cache;
        $this->cacheService = $cacheService;
    }

    public function run($limit = null)
    {
        if ($this->key === null) {
            return static::fromDelta(null, $this->cache, $limit, $this->settings);
        }

        $frontpageSettings = $this->sessionHandler->getDataWithKey('frontpage');
        if ($frontpageSettings === null or !is_array($frontpageSettings)) {
            return static::fromDelta(null, $this->cache, $limit, $this->settings);
        }

        if (!isset($frontpageSettings[$this->key])) {
            return static::fromDelta(null, $this->cache, $limit, $this->settings);
        }

        return static::fromDelta($frontpageSettings[$this->key], $this->cache, $limit, $this->settings);
    }
}

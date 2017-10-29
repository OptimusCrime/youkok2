<?php
namespace Youkok\Processors\PopularListing;

use Youkok\Helpers\SessionHandler;

abstract class AbstractPopularListingProcessor
{
    private $sessionHandler;
    private $cache;
    private $key;
    private $settings;

    protected function __construct(SessionHandler $sessionHandler, $key = null)
    {
        $this->sessionHandler = $sessionHandler;
        $this->key = $key;
        return $this;
    }

    public function withCache($cache)
    {
        $this->cache = $cache;
        return $this;
    }

    public function withSettings($settings)
    {
        $this->settings = $settings;
        return $this;
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

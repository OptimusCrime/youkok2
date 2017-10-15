<?php
namespace Youkok\Processors;


use Youkok\Helpers\SessionHandler;

abstract class AbstractPopularListingProcessor
{
    private $sessionHandler;
    private $cache;
    private $key;

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

    public function run($limit = null)
    {
        if ($this->key === null) {
            return static::fromDelta(null, $limit, $this->cache);
        }

        $frontpageSettings = $this->sessionHandler->getDataWithKey('frontpage');
        if ($frontpageSettings === null or !is_array($frontpageSettings)) {
            return static::fromDelta(null, $limit, $this->cache);
        }

        if (!isset($frontpageSettings[$this->key])) {
            return static::fromDelta(null, $limit, $this->cache);
        }

        return static::fromDelta($frontpageSettings[$this->key], $limit, $this->cache);
    }
}

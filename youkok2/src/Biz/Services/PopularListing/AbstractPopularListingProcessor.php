<?php
namespace Youkok\Biz\Services\PopularListing;

use Youkok\Biz\Services\Cache\CacheService;
use Youkok\Biz\Services\SessionService;

abstract class AbstractPopularListingProcessor
{
    protected $sessionHandler;
    protected $cacheService;

    public function __construct(SessionService $sessionHandler, CacheService $cacheService) {
        $this->sessionHandler = $sessionHandler;
        $this->cacheService = $cacheService;
    }
}

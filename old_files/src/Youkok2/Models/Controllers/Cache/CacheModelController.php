<?php
namespace Youkok2\Models\Controllers\Cache;

use Youkok2\Models\Controllers\BaseController;

abstract class CacheModelController extends BaseController
{
    
    public static $cacheKey;

    public function __construct($class, $model) {
        parent::__construct($class, $model);
    }
}

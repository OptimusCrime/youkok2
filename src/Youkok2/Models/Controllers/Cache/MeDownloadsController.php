<?php
namespace Youkok2\Models\Controllers\Cache;

class MeDownloadsController extends CacheModelController
{
    
    public static $cacheKey = 'md';

    public function __construct($model) {
        parent::__construct($this, $model);
    }
}

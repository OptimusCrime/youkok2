<?php
/*
 * File: CacheModelController.php
 * Holds: Controller for the cache models
 * Created: 04.07.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\Controllers\Cache;

use Youkok2\Models\Controllers\BaseController;

abstract class CacheModelController extends BaseController
{
    /*
     * Variables
     */

    public static $cacheKey;

    /*
     * Constructor
     */

    public function __construct($class, $model) {
        parent::__construct($class, $model);
    }
}

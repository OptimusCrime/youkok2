<?php
/*
 * File: MeDownloadsController.php
 * Holds: Controller for the model MeDownloads
 * Created: 12.12.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\Controllers\Cache;

class MeDownloadsController extends CacheModelController
{
    
    /*
     * Variables
     */
    
    public static $cacheKey = 'md';
    
    /*
     * Constructor
     */

    public function __construct($model) {
        parent::__construct($this, $model);
    }
}

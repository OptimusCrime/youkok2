<?php
/*
 * File: MeDownloadsController.php
 * Holds: Controller for the model MeDownloads
 * Created: 12.12.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\Controllers\Cache;

use Youkok2\Models\Controllers\BaseController;

class MeDownloadsController extends BaseController
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

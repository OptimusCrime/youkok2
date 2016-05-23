<?php
/*
 * File: Processors.php
 * Holds: Processors are views, all the processors classes are extended from this view
 * Created: 11.09.2014
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Models\Controllers\Cache\MeDownloadsController;
use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Loader;

class Processors extends BaseView
{
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Run the view
     */

    public function run() {
        // Check if we should return as json
        if ($this->getSetting('application') and php_sapi_name() != 'cli' and !isset($_GET['format'])) {
            $this->application->setHeader('Content-Type', 'application/json');
        }
    }
    
    /*
     * Static method used to check if a view is a processor or not
     */
    
    public static function isProcessor() {
        return true;
    }
}

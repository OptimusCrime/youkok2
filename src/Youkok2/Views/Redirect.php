<?php
/*
 * File: Redirect.php
 * Holds: Handles redirecting links
 * Created: 11.09.2014
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Models\Controllers\Cache\MeDownloadsController;
use Youkok2\Utilities\CacheManager;

class Redirect extends BaseView
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
        // Parse query
        $query = explode('/', $this->path);
        
        // Check query
        if (count($query) > 0) {
            $id = $query[count($query) - 1];
            if (is_numeric($id)) {
                $element = Element::get($id);
                
                // Check if everything is good
                if ($element->wasFound() and !$element->isPending() and !$element->isDeleted() and $element->isLink()) {
                    // All good, check if we should count or ignore redirect
                    if (!isset($_GET['donotlogthisdownload'])) {
                        // Log download
                        $element->addDownload();
                        
                        // Check if the current user is logged in
                        if (Me::isLoggedIn()) {
                            // Clear the cache for the MeDownloads element
                            CacheManager::deleteCache(Me::getId(), MeDownloadsController::$cacheKey);
                        }
                    }
                    
                    // Redirect
                    $this->application->send($element->getUrl(), true);
                }
            }
        }

        // If we got this far, something is fucked up!
        $this->display404();
    }
}

<?php
namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Models\Controllers\Cache\MeDownloadsController;
use Youkok2\Utilities\CacheManager;

class Redirect extends BaseView
{
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        $query = explode('/', $this->path);
        
        if (count($query) > 0) {
            $id = $query[count($query) - 1];
            if (is_numeric($id)) {
                $element = Element::get($id);
                
                if ($element->wasFound() and !$element->isPending() and !$element->isDeleted() and $element->isLink()) {
                    // All good, check if we should count or ignore redirect
                    if (!isset($_GET['donotlogthisdownload'])) {
                        $element->addDownload($this->me);
                        
                        if ($this->me->isLoggedIn()) {
                            // Clear the cache for the MeDownloads element
                            CacheManager::deleteCache($this->me->getId(), MeDownloadsController::$cacheKey);
                        }
                    }
                    
                    $this->application->send($element->getUrl(), true);
                }
            }
        }

        $this->display404();
    }
}

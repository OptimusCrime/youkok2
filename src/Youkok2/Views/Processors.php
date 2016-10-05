<?php
namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Models\Controllers\Cache\MeDownloadsController;
use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Loader;

class Processors extends BaseView
{
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        if ($this->getSetting('application') and (php_sapi_name() != 'cli' or !isset($_GET['format']))) {
            $this->application->setHeader('Content-Type', 'application/json');
        }
    }
    
    public static function isProcessor() {
        return true;
    }
}

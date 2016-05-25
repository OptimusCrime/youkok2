<?php
/*
 * File: StaticReturner.php
 * Holds: Returns static content
 * Created: 16.12.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2\Processors;

use Youkok2\Utilities\Loader;

class StaticReturner extends BaseProcessor
{
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Load data
     */
    
    public function run() {
        // Figure out what is called
        if ($this->path == '/processor/search/courses.json') {
            $file = CACHE_PATH . '/courses.json';
            if (file_exists($file)) {
                $content = file_get_contents($file);
            }
            else {
                $content = [];
            }
        }
        
        if ($this->getSetting('application')) {
            $this->application->setBody($content);
        }
    }
}

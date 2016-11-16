<?php
namespace Youkok2\Processors;

use Youkok2\Utilities\Loader;

class StaticReturner extends BaseProcessor
{
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function run() {
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

<?php
/*
 * File: NotFound.php
 * Holds: Returns error because processor was not found
 * Created: 02.10.2013
 * Project: Youkok2
 * 
 */

namespace Youkok2\Processors;

class NotFound extends BaseProcessor {
    
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
        parent::run();
        
        // Set data
        $this->setData('msg', 'Processor not found');
        $this->setData('code', 500);
        
        // Handle output
        $this->handleOutput();
    }
}
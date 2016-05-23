<?php
/*
 * File: ModuleProcessor.php
 * Holds: Base module processor class
 * Created: 29.11.2015
 * Project: Youkok2
*/

namespace Youkok2\Processors\Modules;

use Youkok2\Processors\BaseProcessor;

abstract class ModuleProcessor extends BaseProcessor
{
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Get the module
     */
    
    public function get() {
        parent::run();
    }
    
    /*
     * Update the module
     */
    
    public function update() {
        parent::run();
    }
}

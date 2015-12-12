<?php
/*
 * File: ModuleProcessor.php
 * Holds: Base module processor class
 * Created: 29.11.2015
 * Project: Youkok2
*/

namespace Youkok2\Processors\Modules;

use Youkok2\Processors\BaseProcessor;

abstract class ModuleProcessor extends BaseProcessor {
    
    /*
     * Constructor
     */
    
    public function __construct($method, $settings) {
        parent::__construct($method, $settings);
    }
    
    /*
     * Get the module
     */
    
    public function get() {
    }
    
    /*
     * Update the module
     */
    
    public function update() {
    }
}
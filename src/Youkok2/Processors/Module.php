<?php
/*
 * File: Module.php
 * Holds: Change module settings
 * Created: 11.01.2015
 * Project: Youkok2
 * 
*/

namespace Youkok2\Processors;

use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Models\Controllers\ElementController;
use Youkok2\Utilities\ClassParser;
use Youkok2\Utilities\Database;

class Module extends BaseProcessor {
    
    /*
     * List of modules
     */
    
    private static $modules = [
        '\Youkok2\Processors\Modules\MostPopularElements' => 1,
        '\Youkok2\Processors\Modules\MostPopularCourses' => 2,
    ];
    
    /*
     * Module to run
     */
    
    private $module = null;
    
    /*
     * Override
     */

    protected function canBeLoggedIn() {
        return true;
    }
    
    /*
     * Override
     */

    protected function requireDatabase() {
        return true;
    }

    /*
     * Override
     */

    protected function encodeData($data) {
        $new_data = [];

        // Loop the data array and run method on each element
        if (isset($data['data']) and count($data['data']) > 0) {
            foreach($data['data'] as $v) {
                $new_data[] = $v->toArray();
            }
        }

        // Set new value
        $data['data'] = $new_data;

        // Return the updated array
        return $data;
    }
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Returns the correct module
     */
    
    private function getModule() {
        // Get the correct module
        foreach (self::$modules as $k => $v) {
            if ($v == $this->getSetting('module')) {
                $this->module = $k;
                break;
            }
        }
    }
    
    /*
     * Fetch module data
     */
    
    public function get() {
        parent::run();
        
        // Set the correct module
        $this->getModule();
        
        // Make sure we tell the model processor not to output anything
        $settings = $this->getSettings();
        $settings['application'] = false;
        
        // Check if the current module exists
        if ($this->module != null) {
            $module_instance = $this->application->runProcessor(new ClassParser('Processors\Modules\MostPopularElements', 'get'), 
                $settings);
            
            // Set the data
            $this->setData('data', $module_instance->getData()['data']);
            
            // Set ok
            $this->setOK();
        }
        else {
            // Module does not exist
            $this->setError();
        }
    }
    
    /*
     * Update module information
     */
    
    public function update() {
        // Set the correct module
        $this->getModule();
        
        // Make sure we tell the model processor not to output anything
        $settings = $this->getSettings();
        $settings['application'] = false;
        
        // Check if the current module exists
        if ($this->module != null) {
            // Create a new instance of the module
            $module_instance = $this->application->runProcessor(new ClassParser('Processors\Modules\MostPopularElements', 'update'), 
                $settings);
            
            // Set the data
            $this->setData('data', $module_instance->getData()['data']);
            
            // Set ok
            $this->setOK();
        }
        else {
            // Module does not exist
            $this->setError();
        }
    }
}
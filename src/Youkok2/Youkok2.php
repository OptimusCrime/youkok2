<?php
/*
 * File: Youkok2.php
 * Holds: The definite base class for the entire system
 * Created: 01.11.14
 * Project: Youkok2
 * 
 */

namespace Youkok2;

use Youkok2\Utilities\Routes;

class Youkok2 {
    
    /*
     * Run a processor with a given action
     */
    
    public static function runProcessor($action, $settings = []) {
        // Check if we should return as json
        if (php_sapi_name() != 'cli' and !isset($_GET['format']) and (isset($settings['output']) and $settings['output'])) {
            header('Content-Type: application/json');
        }
        
        // Check override
        if (isset($_GET['format'])) {
            if ($_GET['format'] == 'json') {
                header('Content-Type: application/json');
            }
        }
        
        // Loop the path-array and find what view to load
        $found = false;
        $processor = 'Youkok2\\Processors\\';
        $method = 'run';
        $processors = Routes::getProcessors();
        
        // Loop the routes
        foreach ($processors as $k => $v) {
            foreach ($v as $iv) {
                if ($iv['path'] == $action or substr($iv['path'], 1) == $action) {
                    // We found matching url-pattern, store name
                    $processor .= $k;
                    
                    // Check if we should call a given method
                    if (isset($iv['method'])) {
                        $method = $iv['method'];
                    }
                    
                    // Set to found and break out of the 
                    $found = true;
                    break;
                }
            }
        }
        
        // Check if found
        if (!$found) {
            $processor .= 'NotFound';
        }
        
        // New instance of processor, let the magic happen
        $processor = new $processor($method, $settings);

        // Return the content
        return $processor->getData();
    }
}
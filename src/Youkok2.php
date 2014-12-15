<?php
/*
 * File: Youkok2.php
 * Holds: The definite base class for the entire system
 * Created: 01.11.14
 * Project: Youkok2
 * 
*/

namespace Youkok2;

/*
 * Define what classes to use
 */

use \Youkok2\Utilities\Routes as Routes;

/*
 * The base class Youkok2
 */

class Youkok2 {
    
    /*
     * Run a processor with a given action
     */
    
    public static function runProcessor($action) {
        // Check if we should return as json
        if (php_sapi_name() !== 'cli') {
            header('Content-Type: application/json');
        }
        
        // Loop the path-array and find what view to load
        $found = false;
        $processor = '\Youkok2\Processors\\';
        $processors = Routes::getProcessors();
        
        // Loop the routes
        foreach ($processors as $k => $v) {
            foreach ($v as $iv) {
                if ($iv == $action or substr($iv, 1) == $action) {
                    // We found matching url-pattern, store name
                    $processor .= $k;
                    $found = true;
                    break;
                }
            }
        }
        
        // Check if found
        if (!$found) {
            $processor .= 'NotFound';
        }
        
        // New instance
        new $processor();
    }
    
}
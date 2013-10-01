<?php
/*
 * File: index.php
 * Holds: The Loader-class that loads the correct class based on the method being called, setting output and including all the stuff we need
 * Created: 02.10.13
 * Last updated: 02.10.13
 * Project: Youkok2
 * 
*/

//
// Debug
//

error_reporting(E_ALL);
ini_set('display_errors', '1');

//
// Timezone GMT+0
//

date_default_timezone_set('Europe/London');

//
// Set headers
//

// TODO
//header('Content-Type: application/json; charset=utf-8');

//
// Include the rest-class, functions, libs etc
//

require_once 'user.php';
require_once 'base.php';
require_once 'local.php';

//
// Trying to include local.php
//

//
// The Loader-class, loads the correct class extended from REST depending on the method being called
//

class Loader {

    //
    // Internal variables
    //
    
    private $paths = array(
        'home' => array('/'),
        'archive' => array('/arkiv'),
        'profile' => array('/profil'),
        'download' => array('/last-ned'),
        'flat' => array('/om','/derp'),
        'notfound' => array('/404'),
    );

    //
    // Constructor
    //

    public function __construct() {
        // Checking wether the path is set or not
        if (isset($_GET['q'])) {
            // We have a path, find the base-path to include the correct script
            if (strpos($_GET['q'],'/') !== false) {
                // We have multiple slashed, use the first one as base for path-lookup
                $path_split = explode('/',$_GET['q']);
                $path = '/'.$path_split[0];
            }
            else {
                // We don't have any slashes in the url, use what we got
                $path = '/'.str_replace('/','',$_GET['q']);
            }
        }
        else {
            // Clean path, use a single shash to identify home-page
            $path = '/';
        }
        
        // Storing the controller to load
        $controller = null;
        
        // Loop the path-array and find what controller to load
        foreach ($this->paths as $k => $v) {
            // Loop the inner array
            foreach ($v as $iv) {
                // Check for match
                if ($iv == $path) {
                    // We found matching url-pattern, store controllername
                    $controller = $k;
                    break;
                }
            }
        }
        
        // Build controller path
        $file = $this->buildControllerPath($controller);
        
        // Checking to see if the file exsists
        if (file_exists($file)) {
            // File exists, load it
            require_once $file;
        }
        else {
            // Load not found
            require_once $this->buildControllerPath('notfound');
        }
    }
    
    //
    // Return full path for a controller
    //
    
    private function buildControllerPath($controller) {
        return dirname(__FILE__).'/controllers/'.strtolower(str_replace(array('.','/'),'',$controller)).'Controller.php';
    }
}

//
// Initiating the loader
//

$loader = new Loader();
?>
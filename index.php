<?php
/*
 * File: index.php
 * Holds: The Loader class that loads the correct class based on the method being called, setting output and including all the stuff we need
 * Created: 02.10.13
 * Last updated: 02.05.14
 * Project: Youkok2
 * 
*/

//
// Timezone GMT+0
//

date_default_timezone_set('Europe/London');

//
// Set headers
//

header('Content-Type: text/html; charset=utf-8');

//
// Define base for this file
//

$base_path = dirname(__FILE__);

//
// Include the libraries and system-files
//

require_once $base_path . '/libs/smarty/Smarty.class.php';
require_once $base_path . '/libs/bcrypt/bcrypt.php';
require_once $base_path . '/libs/phpmailer/class.phpmailer.php';

require_once $base_path . '/elements/collection.class.php';
require_once $base_path . '/elements/item.class.php';
require_once $base_path . '/elements/user.class.php';

require_once $base_path . '/base.php';
require_once $base_path . '/executioner.php';
require_once $base_path . '/local.php';

//
// Debug
//

error_reporting(SITE_ERROR_REPORTING);
ini_set('display_errors', SITE_ERROR_DISPLAY);

//
// The Loader-class, loads the correct class extended from REST depending on the method being called
//

class Loader {

    //
    // Internal variables
    //
    
    private $basePath;
    private $paths = array(
        'home' => array(
            '/',
        ),

        'archive' => array(
            '/kokeboka',
        ),

        'profile' => array(
            '/profil',
        ),

        'download' => array(
            '/last-ned',
        ),

        'flat' => array(
            '/om',
            '/retningslinjer',
            '/privacy',
            '/hjelp',
            '/karma',
            '/changelog.txt',
        ),

        'notfound' => array(
            '/404',
        ),

        'other' => array(
            '/wall-of-shame',
        ),

        'processor' => array(
            '/processor',
        ),

        'auth' => array(
            '/logg-inn',
            '/logg-ut',
            '/registrer',
            '/glemt-passord',
            '/nytt-passord',
            '/verifiser',
        ),
        
        'graybox' => array(
            '/graybox',
        ),
    );

    //
    // Constructor
    //

    public function __construct($base_path) {
        // Store the base path for the project
        $this->basePath = $base_path;

        // Checking wether the path is set or not
        if (isset($_GET['q'])) {
            // We have a path, find the base-path to include the correct script
            if (strpos($_GET['q'], '/') !== false) {
                // We have multiple slashed, use the first one as base for path-lookup
                $path_split = explode('/', $_GET['q']);
                $path = '/' . $path_split[0];
            } else {
                // We don't have any slashes in the url, use what we got
                $path = '/' . str_replace('/', '', $_GET['q']);
            }
        } else {
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
        } else {
            // Load not found
            require_once $this->buildControllerPath('notfound');
        }
    }
    
    //
    // Return full path for a controller
    //
    
    private function buildControllerPath($controller) {
        return dirname(__FILE__) . '/controllers/' . strtolower(str_replace(array('.', '/'), '', $controller)) . 'Controller.php';
    }
    
    //
    // Returning the paths
    //
    
    public function getPaths() {
        return $this->paths;
    }

    //
    // Returning the base path
    //

    public function getBasePath() {
        return $this->basePath;
    }
}

//
// Initiating the loader
//

$loader = new Loader($base_path);
?>
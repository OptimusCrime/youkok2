<?php
/*
 * File: index.php
 * Holds: The Loader class that loads the correct class based on the method being called, setting output and including 
 *        all the stuff we need
 * Created: 02.10.13
 * Project: Youkok2
 * 
*/

//
// Set headers
//

header('Content-Type: text/html; charset=utf-8');

//
// Include the libraries and system-files
//

require_once 'local.php';

require_once BASE_PATH . '/libs/bcrypt/bcrypt.php';
require_once BASE_PATH . '/libs/pdo2/pdo2.class.php';
require_once BASE_PATH . '/libs/pdo2/pdostatement2.class.php';
require_once BASE_PATH . '/libs/phpmailer/class.phpmailer.php';
require_once BASE_PATH . '/libs/smarty/Smarty.class.php';
require_once BASE_PATH . '/libs/youkok2/cachemanager.class.php';
require_once BASE_PATH . '/libs/youkok2/executioner.class.php';
require_once BASE_PATH . '/libs/youkok2/utilities.class.php';

require_once BASE_PATH . '/elements/collection.class.php';
require_once BASE_PATH . '/elements/course.class.php';
require_once BASE_PATH . '/elements/history.class.php';
require_once BASE_PATH . '/elements/item.class.php';
require_once BASE_PATH . '/elements/flag.class.php';
require_once BASE_PATH . '/elements/user.class.php';
require_once BASE_PATH . '/elements/vote.class.php';

require_once BASE_PATH . '/controllers/youkok2.controller.php';

//
// Debug
//

error_reporting(SITE_ERROR_REPORTING);
ini_set('display_errors', SITE_ERROR_DISPLAY);

//
// Timezone
//

date_default_timezone_set(SITE_TIMEZONE);

//
// The Loader-class, loads the correct class extended from REST depending on the method being called
//

class Loader {

    //
    // Internal variables
    //
    
    private $routes = array(
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

        'search' => array(
            '/sok',
        )
     );

    //
    // Constructor
    //

    public function __construct() {
        // Checking wether the path is set or not
        if (isset($_GET['q'])) {
            // We have a path, find the base-path to include the correct script
            if (strpos($_GET['q'], '/') !== false) {
                // We have multiple slashed, use the first one as base for path-lookup
                $path_split = explode('/', $_GET['q']);
                $path = '/' . $path_split[0];
            }
            else {
                // We don't have any slashes in the url, use what we got
                $path = '/' . str_replace('/', '', $_GET['q']);
            }
        } else {
            // Clean path, use a single shash to identify home-page
            $path = '/';
        }
        
        // Storing the controller to load
        $controller_filename = '';
        
        // Loop the path-array and find what controller to load
        foreach ($this->routes as $k => $v) {
            // Loop the inner array
            foreach ($v as $iv) {
                // Check for match
                if ($iv == $path) {
                    // We found matching url-pattern, store controllername
                    $controller_filename = $k;
                    break;
                }
            }
        }
        
        // Build controller path
        $file = $this->buildControllerPath($controller_filename);
        
        // Checking to see if the file exsists
        if (file_exists($file)) {
            // File exists, load it
            $controller = require_once $file;
        }
        else {
            // Load not found
            $controller = require_once $this->buildControllerPath('notfound');
        }

        // Run instance
        new $controller($this->routes);
    }
    
    //
    // Return full path for a controller
    //
    
    private function buildControllerPath($name) {
        return BASE_PATH
             . '/controllers/'
             . strtolower(str_replace(array('.', '/'), '', $name))
             . '.controller.php';
    }
}

//
// Initiating the loader
//

$loader = new Loader();
?>
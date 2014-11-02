<?php
/*
 * File: Loader.php
 * Holds: The Loader class that runs a new instance of the view based on the url matching from Routes
 * Created: 02.11.2014
 * Project: Youkok2
 */
 
namespace Youkok2\Utilities;

/*
 * The Loader class. Loads the correct view based on the url
 */

class Loader {
    
    /*
     * Constructor
     */

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
            // Clean path, use a single slash to identify home-page
            $path = '/';
        }

        // Storing the namespace for the view to call
        $view = '\Youkok2\Views\\';

        // Loop the path-array and find what view to load
        $routes = Routes::getRoutes();
        $found = false;
        
        foreach ($routes as $k => $v) {
            foreach ($v as $iv) {
                if ($iv == $path) {
                    // We found matching url-pattern, store name
                    $view .= $k;
                    $found = true;
                    break;
                }
            }
        }
        
        // Check to see if we actually found a route
        if (!$found) {
            $view .= 'NotFound';
        }

        // Run instance
        new $view();
    }
}
<?php
/*
 * File: Loader.php
 * Holds: The Loader class that runs a new instance of the view based on the url matching from Routes
 * Created: 02.11.2014
 * Project: Youkok2
 */
 
namespace Youkok2\Utilities;

/*
 * Define what classes to use
 */

use \Youkok2\Youkok2 as Youkok2;

/*
 * The Loader class. Loads the correct view based on the url
 */

class Loader {
    
    /*
     * Constructor
     */
    
    private $basePath;
    private $fullPath;
    private $pathLength;

    public function __construct() {
        // Get the base path (/[something])
        $this->getBasePath();

        // Check if proseccor or view is requested
        if ($this->basePath == Routes::PROSECESSOR) {
            // Get processor
            $this->getProcessor();
        }
        else {
            $this->getView();
        }
    }
    
    /*
     * Returns processor
     */
    
    private function getProcessor() {
        // Trim the fullPath
        $action = substr(str_replace(Routes::PROSECESSOR, '', $this->fullPath), 1);

        // Run processor
        Youkok2::runProcessor($action);
    }
    
    /*
     * Returns view
     */
    
    private function getView() {
        // Loop the path-array and find what view to load
        $found = false;
        $view = '\Youkok2\Views\\';
        $method = null;
        $routes = Routes::getRoutes();
        
        // Loop the routes
        foreach ($routes as $k => $v) {
            foreach ($v as $iv) {
                if ($iv['path'] == $this->basePath) {
                    // Holds validity for this route
                    $valid = true;
                    
                    // Check for subpath
                    if (isset($iv['subpath'])) {
                        if ($iv['subpath'] === true and $this->pathLength == 1) {
                            $valid = false;
                        }
                        else if ($iv['subpath'] === false and $this->pathLength > 1) {
                            $valid = false;
                        }
                    }
                    
                    // Check if valid
                    if ($valid) {
                        // We found matching url-pattern, store name
                        $view .= $k;
                        
                        // Check if this path has own method
                        if (isset($iv['method'])) {
                            $method = $iv['method'];
                        }
                        
                        // Update found and exit loop
                        $found = true;
                        break;
                    }
                }
            }
        }
        
        // If not found, display 404 view
        if (!$found) {
            // Array with regex expressions
            $regexes = array(
                '/' => '\/', 
                '*' => '(.*)'
            );
            
            // Check if should be redirected
            $redirects = Routes::getRedirects();
            
            // Loop all redirects
            foreach ($redirects as $k => $v) {
                // Build regex patthern
                $regex = '/^' . str_replace(array_keys($regexes), $regexes, $k) . '/i';
                
                // Test regex
                if (preg_match_all($regex, $this->fullPath, $matches)) {
                    $redirect_url = URL_FULL . substr(str_replace('*', $matches[1][0], $v), 1);
                    
                    // Send redirect
                    header('HTTP/1.1 301 Moved Permanently'); 
                    header('Location: ' . $redirect_url);
                    
                    // Kill
                    exit();
                }
            }
            
            // If we got this far, we never found a match
            $view .= 'NotFound';
        }
        
        // Run instance
        $view_instance = new $view();
        
        // Check if we should call method on view
        if ($method !== null) {
            $view_instance->$method();
        }
    }
    
    /*
     * Returns the base path for the request
     */
    
    private function getBasePath() {
        // Checking wether the path is set or not

        $request_path = self::getQuery();

        if (isset($request_path)) {
            // Store the paths first
            $this->basePath = '/' . $request_path;
            $this->fullPath = '/' . $request_path;
            
            // Set path length to 1
            $this->pathLength = 1;
            
            // We have a path, find the base-path to include the correct script
            if (strpos($request_path, '/') !== false) {
                // We have multiple slashed, use the first one as base for path-lookup
                $path_split = explode('/', $request_path);
                $this->basePath = '/' . $path_split[0];
                
                // Update path to the actual number
                $this->pathLength = 0;
                foreach ($path_split as $path_split_seq) {
                    if (strlen($path_split_seq) > 0) {
                        $this->pathLength++;
                    }
                }
            }
        }
        else {
            // Store full path
            $this->basePath = '/';
            $this->fullPath = '/';
            
            // Set path length to 1
            $this->pathLength = 1;
        }
    }

    /*
     *  * Get request path
     */

    public static function getQuery() {
        // Check if we are running built in server or apache/nginx
        if (strpos($_SERVER['SERVER_SOFTWARE'], 'Development Server') !== false) {
            $request_url = $_SERVER['REQUEST_URI'];

            // Check if request uri has additional information (? params)
            if (strpos($request_url, '?') !== false) {
                $request_url = explode('?', $request_url)[0];
            }

            // PHP built in server
            return substr($request_url, 1);
        }
        else {
            // Apache/nginx/etc
            return (isset($_GET['q']) ? $_GET['q'] : '/');
        }
    }
}
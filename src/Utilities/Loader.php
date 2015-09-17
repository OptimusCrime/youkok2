<?php
/*
 * File: Loader.php
 * Holds: The Loader class that runs a new instance of the view based on the url matching from Routes
 * Created: 02.11.2014
 * Project: Youkok2
 * 
 */
 
namespace Youkok2\Utilities;

use \Youkok2\Youkok2 as Youkok2;

class Loader {
    
    /*
     * Constructor
     */

    private static $query;
    private $override;
    private $basePath;
    private $fullPath;
    private $pathLength;
    private $providedPath;
    private $match;


    public function __construct($path = null) {
        // Check if overriding
        if ($path !== null) {
            // Overriding
            $this->override = true;
            $this->providedPath = $path;
        }
        else {
            // We are not overriding
            $this->override = false;
        }
        
        // Get the correct (dynamic) base path
        $this->getBasePath();

        // Analyze the path
        self::queryAnalyze();
        
        // Check if proseccor or view is requested
        if ($this->basePath == Routes::PROCESSOR) {
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
        $action = substr(str_replace(Routes::PROCESSOR, '', $this->fullPath), 1);

        // Run processor
        Youkok2::runProcessor($action, ['output' => true, 'encode' => true]);
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
        
        // Check if override
        if ($this->override) {
            // We are overriding, simply set view and method, if set
            $this->match = $view . ($method === null ? '' : '.' . $method);
        }
        else {
            // Run instance
            $view_instance = new $view();
            
            // Check if we should call method on view
            if ($method !== null) {
                $view_instance->$method();
            }
        }
    }
    
    /*
     * Returns the base path for the request
     */
    
    private function getBasePath() {
        // Check if overriding
        if ($this->override) {
            $request_path = $this->providedPath;
        }
        else {
            // Checking wether the path is set or not
            $request_path = self::getQuery();
        }
        
        if (isset($request_path)) {
            // We have a path, find the base-path to include the correct script
            if ($request_path == '' or $request_path == '/') {
                // Store the paths first
                $this->basePath = '/';
                $this->fullPath = '/';
                
                // Set path length to 1
                $this->pathLength = 1;
            }
            elseif (strpos($request_path, '/') !== false) {
                // We have multiple slashed, use the first which has a length one as base for path-lookup
                $path_split = explode('/', $request_path);
                
                // Clean the path
                $path_clean = [];
                foreach ($path_split as $path_split_seq) {
                    if (strlen($path_split_seq) > 0) {
                        $path_clean[] = $path_split_seq;
                    }
                }
                
                // Check if anything was found after cleaning
                if (count($path_clean) > 0) {
                    $this->basePath = '/' . $path_clean[0];
                    $this->pathLength = count($path_clean);
                }
                else {
                    // Simply set the entire url as params, something is fucked
                    $this->basePath = '/' . $request_path;
                    
                    // Get number of slashes in string
                    $this->pathLength = substr_count($request_path, '/');
                }
                
                // Set full path to everything either way
                $this->fullPath = '/' . $request_path;
            }
            else {
                // Store the paths first
                $this->basePath = '/' . $request_path;
                $this->fullPath = '/' . $request_path;
                
                // Set path length to 1
                $this->pathLength = 1;
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
    
    /*
     * Return whatever was matches
     */
    
    public function getMatch() {
        return $this->match;
    }

    /*
     * Methods for analyzing, reading and returning the query
     */

    private static function queryAnalyze() {
        // Init array
        self::$query = [];

        // Split query
        $q = explode('/', Loader::getQuery());

        // Read fragments
        if (count($q) > 0) {
            foreach ($q as $v) {
                if (strlen($v) > 0) {
                    self::$query[] = $v;
                }
            }
        }
    }

    /*
     * Get the length of the query
     */

    public static function queryGetSize() {
        return count(self::$query);
    }

    /*
     * Get a fragment from the query
     */

    public static function queryGet($i, $prefix = '', $endfix = '') {
        if (count(self::$query) >= $i) {
            return $prefix . self::$query[$i] . $endfix;
        }
    }

    /*
     * Get the entire query
     */

    public static function queryGetClean($prefix = '', $endfix = '') {
        if (count(self::$query) > 0) {
            return $prefix . implode('/', self::$query) . $endfix;
        }
        else {
            return null;
        }
    }
}
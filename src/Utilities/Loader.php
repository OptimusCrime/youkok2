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
        $routes = Routes::getRoutes();
        
        // Loop the routes
        foreach ($routes as $k => $v) {
            foreach ($v as $iv) {
                if ($iv == $this->basePath) {
                    // We found matching url-pattern, store name
                    $view .= $k;
                    $found = true;
                    break;
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
                    $redirect_url = URL_FULL . substr(str_replace('*', $matches[0][0], $v), 1);
                    
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
        new $view();
    }
    
    /*
     * Returns the base path for the request
     */
    
    private function getBasePath() {
        // Checking wether the path is set or not
        if (isset($_GET['q'])) {
            // Store the paths first
            $this->basePath = '/' . $_GET['q'];
            $this->fullPath = '/' . $_GET['q'];
            
            // We have a path, find the base-path to include the correct script
            if (strpos($_GET['q'], '/') !== false) {
                // We have multiple slashed, use the first one as base for path-lookup
                $path_split = explode('/', $_GET['q']);
                $this->basePath = '/' . $path_split[0];
            }
        }
        else {
            // Store full path
            $this->basePath = '/';
            $this->fullPath = '/';
        }
    }
}
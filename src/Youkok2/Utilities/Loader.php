<?php
/*
 * File: Loader.php
 * Holds: The Loader class that runs a new instance of the view based on the url matching from Routes
 * Created: 02.11.2014
 * Project: Youkok2
 * 
 */
 
namespace Youkok2\Utilities;

use Youkok2\Youkok2;

class Loader {
    
    /*
     * Returns a class using the provided URL path
     */
    
    public static function getClass($path) {
        // Loop the path-array and find what view to load
        $found = false;
        $view_path = '\Youkok2\\';
        $method = null;
        $routes = Routes::getRoutes();
        
        // Clean the current URL
        $current_url_clean = self::cleanPath($path);
        
        // Loop all the routes
        foreach ($routes as $view => $list) {
            // Break if we found a valid view
            if ($found) {
                break;
            }
            
            // Loop the list of routes for this view
            foreach ($list as $v) {
                // Clean this route to remove any errors
                $route_clean = self::cleanPath($v['path']);
                
                // Handle edge case where we browse the frontpage
                if (count($current_url_clean) == 0 and count($route_clean) == 0) {
                    $found = true;
                }
                
                // No edge case, search for view by maching sub queries
                $valid = true;
                for ($i = 0; $i < count($current_url_clean); $i++) {
                    // Make sure the sub query is found in both the queries
                    if (isset($route_clean[$i])) {
                        // If the sub queries does not match, only a + makes it valid
                        if ($current_url_clean[$i] != $route_clean[$i]) {
                            if ($route_clean[$i] != '+') {
                                $valid = false;
                                break;
                            }
                            else {
                                break;
                            }
                        }
                    }
                    else {
                        $valid = false;
                        break;
                    }
                }
                
                // Check if we found the curret path
                if ($valid) {
                    $found = true;
                    $view_path .= $view;
                    
                    if (isset($v['method'])) {
                        $method = $v['method'];
                    }
                    
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
                if (preg_match_all($regex, $path, $matches)) {
                    $redirect_url = URL_FULL . substr(str_replace('*', $matches[1][0], $v), 1);
                    
                    // Send redirect
                    header('HTTP/1.1 301 Moved Permanently'); 
                    header('Location: ' . $redirect_url);
                    
                    // Kill
                    exit();
                }
            }
            
            // If we got this far, we never found a match
            $view_path .= 'Views\NotFound';
        }
        
        // Return the view and method
        return [
            'view' => $view_path,
            'method' => $method
        ];
    }
    
    /*
     * Cleans a path
     */
    
    private static function cleanPath($path) {
        $path_clean = [];
        $path_split = explode('/', $path);
        foreach ($path_split as $v) {
            if (strlen($v) > 0) {
                $path_clean[] = $v;
            }
        }
        
        return $path_clean;
    }
}
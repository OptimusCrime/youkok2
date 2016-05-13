<?php
/*
 * File: TemplateHelper.php
 * Holds: Methods used by smarty
 * Created: 28.11.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2\Utilities;

use Youkok2\Utilities\Loader;

class TemplateHelper {

    /*
     * Method for reverse lookup for URL creation
     */

    public static function url_for($identifier, $params = null) {
        // Get the routes
        $routes = Routes::getRoutes();
        
        // Find the correct route
        $route = null;
        foreach ($routes as $collection) {
            foreach ($collection as $v) {
                if (isset($v['identifier']) and $v['identifier'] == $identifier) {
                    $route = $v;
                    break;
                }
            }
        }
        
        // Make sure a results was found
        if ($route === null) {
            return '';
        }
        
        // We have a valid result, check if any params was provided
        $path = '';
        if ($params !== null) {
            // We have params, which means we have to construct a URL
            $url_dirty = explode('/', $route['path']);
            $url_clean = [];
            foreach ($url_dirty as $v) {
                if (strlen($v) > 0) {
                    $url_clean[] = $v;
                }
            }
            
            $url_constructed = [];
            foreach ($url_clean as $v) {
                // Check if we have a wildecard or not present
                if ($v != '+') {
                    // No wildcard
                    $url_constructed[] = $v;
                }
                else {
                    // Yes wildcard!
                    foreach ($params as $param) {
                        $url_constructed[] = preg_replace('/\+/', $param, $route['construct']);
                    }
                }
            }
            
            // Implode the final path
            $path = implode('/', $url_constructed);
        }
        else {
            // Simplu return the path string
            $path = substr($route['path'], 1);
        }
        
        // Check if we should prefix with /
        if (isset($route['prefix']) and $route['prefix']) {
            $path = '/' . $path;
        }
        
        // Check if we should endfix with /
        if (isset($route['endfix']) and $route['endfix']) {
            // We should add an endfix here
            $path .= '/';
        }
        else if (isset($route['endfix']) and !$route['endfix']) {
            // We should make sure to remove any endfix (if present)
            if (substr($path, strlen($path) - 1) == '/') {
                $path = substr($path, 0, strlen($path) - 1);
            }
        }
        
        // Replace two or more occurences of /
        $path = preg_replace('~/{2,}~', '/', $path);
        
        // Return the final path
        return $path;
    }
} 
<?php
namespace Youkok2\Utilities;

class TemplateHelper
{
    
    public static function urlFor($identifier, $params = null) {
        $routes = Routes::getRoutes();
        
        $route = null;
        foreach ($routes as $collection) {
            foreach ($collection as $v) {
                if (isset($v['identifier']) and $v['identifier'] == $identifier) {
                    $route = $v;
                    break;
                }
            }
        }
        
        if ($route === null) {
            return '';
        }
        
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
                    $url_constructed[] = $v;
                }
                else {
                    foreach ($params as $param) {
                        $url_constructed[] = preg_replace('/\+/', $param, $route['construct']);
                    }
                }
            }
            
            $path = implode('/', $url_constructed);
        }
        else {
            $path = substr($route['path'], 1);
        }
        
        // Check if we should endfix with /
        if (isset($route['endfix']) and $route['endfix']) {
            $path .= '/';
        }
        elseif (isset($route['endfix']) and !$route['endfix']) {
            // We should make sure to remove any endfix (if present)
            if (substr($path, strlen($path) - 1) == '/') {
                $path = substr($path, 0, strlen($path) - 1);
            }
        }
        
        // Replace two or more occurences of /
        $path = preg_replace('~/{2,}~', '/', $path);
        
        return $path;
    }
}

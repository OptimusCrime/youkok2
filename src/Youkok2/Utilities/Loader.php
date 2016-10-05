<?php
namespace Youkok2\Utilities;

class Loader
{
    
    public static function getClass($path) {
        $found = false;
        $view_path = '\Youkok2\\';
        $method = null;
        $processor = false;
        $routes = Routes::getRoutes();
        
        $current_url_clean = self::cleanPath($path);
        
        if (count($current_url_clean) > 0 and ('/' . $current_url_clean[0]) == Routes::PROCESSOR) {
            $processor = true;
        }
        
        foreach ($routes as $view => $list) {
            if ($found) {
                break;
            }
            
            foreach ($list as $v) {
                $route_clean = self::cleanPath($v['path']);
                
                if (count($current_url_clean) == 0 and count($route_clean) == 0) {
                    $found = true;
                }
                
                $valid = true;
                for ($i = 0; $i < count($current_url_clean); $i++) {
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
            $regexes = [
                '/' => '\/',
                '*' => '(.*)'
            ];
            
            $redirects = Routes::getRedirects();
            
            foreach ($redirects as $k => $v) {
                // Build regex pattern
                $regex = '/^' . str_replace(array_keys($regexes), $regexes, $k) . '/i';

                
                // Test regex
                if (preg_match_all($regex, $path, $matches)) {
                    $redirect_url = URL_FULL . str_replace('*', $matches[1][0], $v);
                    
                    return [
                        'view' => null,
                        'method' => null,
                        'redirect' => $redirect_url
                    ];
                }
            }
            
            if (!$processor) {
                $view_path .= 'Views\NotFound';
            }
            else {
                $view_path .= 'Processors\NotFound';
            }
        }
        
        return [
            'view' => $view_path,
            'method' => $method
        ];
    }
    
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

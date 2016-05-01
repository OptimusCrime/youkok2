<?php
/*
 * File: QueryParser.php
 * Holds: Parses the query from the web server engine
 * Created: 01.05.2016
 * Project: Youkok2
 * 
 */
 
namespace Youkok2\Utilities;

class QueryParser {

    private static $query;
    private $override;
    private $basePath;
    private $fullPath;
    private $pathLength;
    
    /*
     * Constructor
     */
    
    public function __construct() {
        // Cleaup
        $this->getBasePath();
    }
    
    private function getBasePath() {
        $request_path = self::getQuery();
        
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
        if (isset($_SERVER['SERVER_SOFTWARE']) and strpos($_SERVER['SERVER_SOFTWARE'], 'Development Server') !== false) {
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
    
    public function getPath() {
        return $this->fullPath;
    }
}
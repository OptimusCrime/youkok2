<?php
/*
 * File: JavaScriptLoader.php
 * Holds: Helper class that loads all the Youkok2 JavaScripts
 * Created: 20.08.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Utilities;

class JavaScriptLoader {

    /*
     * Method for redirecting
     */

    public static function get() {
        // Loop and look for files
        $files = self::findFiles([], '/assets/js/youkok/');
       
        // Implode the list and return the result
        return implode(PHP_EOL, $files);
    }
    
    private static function findFiles($files, $path) {
        // Make sure we can open the directory
        if ($dh = opendir(BASE_PATH . $path)) {
            // Loop the directory
            while (($file = readdir($dh)) !== false) {
                // Drop dot and double dot directories because derp
                if ($file != '.' and $file != '..') {
                    // Check if directory or not
                    if (is_dir(BASE_PATH . $path . $file)) {
                        // Current object is directory, reccursion!!
                        $files = self::findFiles($files, $path . $file . '/');
                    }
                    else {
                        // Just a simple file, add to array
                        $files[] = '<script type="text/javascript" src="' . substr($path, 1) . $file . '?v=' . VERSION . '"></script>';
                    }
                }
            }
            
            // Close scan
            closedir($dh);
        }
        
        // Return list of files
        return $files;
    }
} 
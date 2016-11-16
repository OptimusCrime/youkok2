<?php
namespace Youkok2\Utilities;

class JavaScriptLoader
{

    public static function get() {
        $files = self::findFiles([], '/assets/js/youkok/');
       
        return implode(PHP_EOL, $files);
    }
    
    private static function findFiles($files, $path) {
        if ($dh = opendir(BASE_PATH . $path)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' and $file != '..') {
                    if (is_dir(BASE_PATH . $path . $file)) {
                        $files = self::findFiles($files, $path . $file . '/');
                    }
                    else {
                        $script  = '<script type="text/javascript" src="' . substr($path, 1) . $file;
                        $script .= '?v=' . VERSION . '"></script>';

                        $files[] = $script;
                    }
                }
            }
            
            closedir($dh);
        }
        
        return $files;
    }
}

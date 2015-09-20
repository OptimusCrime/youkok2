<?php
/*
 * File: Upgrade.php
 * Holds: Upgrades the system
 * Created: 03.06.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Processors\Tasks;

use \Youkok2\Processors\BaseProcessor as BaseProcessor;
use \Youkok2\Utilities\LineCounter as LineCounter;

class Upgrade extends BaseProcessor {

    /*
     * Override
     */

    protected function checkPermissions() {
        return $this->requireCli() or $this->requireAdmin();
    }
    
    /*
     * Override
     */

    protected function requireDatabase() {
        return true;
    }

    /*
     * Construct
     */

    public function __construct($method, $settings) {
        // Calling Base' constructor
        parent::__construct($method, $settings);
    }
    
    /*
     * Method ran by the processor
     */
    
    public function run() {
        $this->buildJS();
        $this->buildCSS();
    }
    
    /*
     * Reccursivly add js files for the 
    
    /*
     * Build the JS files
     */
    
    private function buildJS() {
        // New instance
        $minifier = new \MatthiasMullie\Minify\JS();
        
        // Use the RecursiveIteratorIterator to loop all subdirectories
        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(BASE_PATH . '/assets/js/youkok/', \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        
        // Use the iterator to loop all the files
        foreach ($iter as $file) {
            $minifier->add($file->getPathname());
        }
        
        // Minify!
        $minifier->minify(BASE_PATH . '/assets/js/youkok.min.temp.js');
        
        // Put content on one line
        $minified_sigle_line = '';
        
        // Read libs
        if ($dh = opendir(BASE_PATH . '/assets/js/libs/')) {
            $js_modules = '';
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' and $file != '..') {
                    $handle = fopen(BASE_PATH . '/assets/js/libs/' . $file, 'r');
                    if ($handle) {
                        while (($line = fgets($handle)) !== false) {
                            $minified_sigle_line .= rtrim($line);
                        }
                    }
                    fclose($handle);
                }
            }
            closedir($dh);
        }
        
        // Read the temp js file
        $handle = fopen(BASE_PATH . '/assets/js/youkok.min.temp.js', 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $minified_sigle_line .= rtrim($line);
            }
        }
        fclose($handle);
        
        file_put_contents(BASE_PATH . '/assets/js/youkok.min.js', $minified_sigle_line);
        
        // Add message
        $this->setData('js', 'Successfully build JS files');
    }
    
    /*
     * Build the CSS files
     */
    
    private function buildCSS() {
        // New instance
        $minifier = new \MatthiasMullie\Minify\CSS();
        
        // Add all files
        $minifier->add(BASE_PATH . '/assets/css/libs/bootstrap.lumen.min.css');
        $minifier->add(BASE_PATH . '/assets/css/youkok.css');
        
        // Minify!
        $minifier->minify(BASE_PATH . '/assets/css/youkok.min.css');
        
        // Add message
        $this->setData('css', 'Successfully build CSS files');
    }
} 
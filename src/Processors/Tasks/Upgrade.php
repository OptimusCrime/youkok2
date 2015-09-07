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
     * Constructor
     */

    public function __construct($outputData = false, $returnData = false) {
        // Calling Base' constructor
        parent::__construct($outputData, $returnData);
        
        // Check permissions
        if (self::requireCli() or self::requireAdmin()) {
            // Build stuff
            $this->buildJS();
            $this->buildCSS();
            
        }
        else {
            // No access
            $this->noAccess();
        }
        
        // Handle output
        if ($this->outputData) {
            $this->outputData();
        }
        if ($this->returnData) {
            return $this->returnData();
        }
    }

    /*
     * Build the JS files
     */
    
    private function buildJS() {
        // New instance
        $minifier = new \MatthiasMullie\Minify\JS();
        
        // Add all module files
        if ($dh = opendir(BASE_PATH . '/assets/js/youkok/')) {
            $js_modules = '';
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' and $file != '..') {
                    $minifier->add(BASE_PATH . '/assets/js/youkok/' . $file);
                }
            }
            closedir($dh);
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
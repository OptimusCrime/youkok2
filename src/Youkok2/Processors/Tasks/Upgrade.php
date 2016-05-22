<?php
/*
 * File: Upgrade.php
 * Holds: Upgrades the system
 * Created: 03.06.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Processors\Tasks;

use Youkok2\Processors\BaseProcessor;
use Youkok2\Utilities\LineCounter;

class Upgrade extends BaseProcessor
{

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
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
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
            if (!$file->isDir()) {
                $minifier->add($file->getPathname());
            }
        }
        
        // Minify!
        $minifier->minify(BASE_PATH . '/assets/js/youkok.min.temp.js');
        
        // Put content on one line
        $js_content = '';
        
        $libs = [
            'jquery-2.1.4.min.js',
            'jquery-ui-1.11.4.min.js',
            'jquery.fileupload.js',
            'jquery.ba-outside-events.min.js',
            'jquery.countdown.min.js',
            'typeahead.bundle.min.js',
            'moment-2.10.3.min.js',
            'underscore-1.8.3.min.js',
            'bootstrap-3.3.5.min.js',
        ];
        
        // Read each lib
        foreach ($libs as $lib) {
            $js_content .= file_get_contents(BASE_PATH . '/assets/js/libs/' . $lib) . PHP_EOL;
        }
        
        // Read the temp js file
        $handle = fopen(BASE_PATH . '/assets/js/youkok.min.temp.js', 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $js_content .= $line;
            }
        }
        fclose($handle);
        
        file_put_contents(BASE_PATH . '/assets/js/youkok.min.js', $js_content);
        
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

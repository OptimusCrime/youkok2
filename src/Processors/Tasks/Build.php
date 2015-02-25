<?php
/*
 * File: Build.php
 * Holds: The Build class, that runs a new build in the system
 * Created: 20.01.15
 * Project: Youkok2
*/

namespace Youkok2\Processors\Tasks;

/*
 * Define what classes to use
 */

use \Youkok2\Processors\Base as Base;
use \Youkok2\Utilities\LineCounter as LineCounter;

/*
 * Build extending Base
 */

class Build extends Base {

    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        parent::__construct($returnData);
        
        // Check permissions
        if (self::requireCli() or self::requireAdmin()) {
            // Build stuff
            $this->buildJS();
            $this->buildCSS();
            
            // Return data
            $this->returnData();
            
            // Run LineCounter
            $this->runLineCounter();
        }
        else {
            // No access
            $this->noAccess();
            $this->returnData();
        }
    }

    /*
     * Build the JS files
     */
    
    private function buildJS() {
        // New instance
        $minifier = new \MatthiasMullie\Minify\JS();
        
        // Add all files
        $minifier->add(BASE_PATH . '/assets/js/libs/typeahead.bundle.min.js');
        $minifier->add(BASE_PATH . '/assets/js/libs/jquery.fileupload.js');
        $minifier->add(BASE_PATH . '/assets/js/libs/jquery.ba-outside-events.min.js');
        $minifier->add(BASE_PATH . '/assets/js/youkok.js');
        $minifier->add(BASE_PATH . '/assets/js/youkok.admin.js');
        
        // Minify!
        $minifier->minify(BASE_PATH . '/assets/js/youkok.min.js');
        
        // Add message
        $this->setData('msg', 'Built JS files');
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
        $this->setData('msg', 'Built CSS files');
    }
    
    /*
     * Run LineCounter and process the output
     */
    
    private function runLineCounter() {
        // New instance of LineCounter with our settings
        $linecounter = new LineCounter(array(
            // Ignoring directories
            '!.git/',
            '!.idea/',
            '!_build/',
            '!assets/',
            '!cache/',
            '!files',
            '!migrations/',
            '!processor/',
            '!templates_c/',
            '!tests/',
            '!vendor/',
            
            // Ignoring files
            '!.htaccess',
            '!.gitignore',
            '!composer.phar',
            '!local.php',
            '!composer.lock',
            '!phinx-example.yml',
            '!phinx.yml',
            
            // Adding some final files
            'assets/js/youkok.js',
            'assets/css/youkok.css',
        ));

        // Analyze
        $linecounter->analyze();
    }
} 
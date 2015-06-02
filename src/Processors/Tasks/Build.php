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

    public function __construct($outputData = false, $returnData = false) {
        // Calling Base' constructor
        parent::__construct($outputData, $returnData);
        
        // Check permissions
        if (self::requireCli() or self::requireAdmin()) {
            // Run LineCounter
            $this->runLineCounter();
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
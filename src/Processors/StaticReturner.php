<?php
/*
 * File: StaticReturner.php
 * Holds: Returns static content
 * Created: 16.12.14
 * Project: Youkok2
 * 
*/

namespace Youkok2\Processors;

/*
 * Define what classes to use
 */

use \Youkok2\Utilities\Loader as Loader;

/*
 * The Static class, extending Base class
 */

class StaticReturner extends BaseProcessor {

    /*
     * Constructor
     */

    public function __construct($method, $settings) {
        // Override settings
        $settings['output'] = false;

        // Calling Base' constructor
        parent::__construct($method, $settings);
    }
    
    /*
     * Return data based on request
     */
    
    protected function run() {
        // Get thequery
        $request = Loader::getQuery();

        // Figure out what is called
        if ($request == 'processor/search/courses.json') {
            $file = CACHE_PATH . '/courses.json';
            if (file_exists($file)) {
                $content = file_get_contents($file);
            }
            else {
                $content = [];
            }
        }

        // Return data (this is dirty as fuck)
        echo $content;
    }
}
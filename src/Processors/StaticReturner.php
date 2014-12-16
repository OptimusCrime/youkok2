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

class StaticReturner extends Base {

    /*
     * Constructor
     */

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();
        
        // Get actual request
        $request = Loader::getQuery();
        
        // Fetch
        $this->fetchData($request);
    }
    
    /*
     * Return data based on request
     */
    
    private function fetchData($request) {
        if ($request == 'processor/search/courses.json') {
            $file = CACHE_PATH . '/courses.json';
            if (file_exists($file)) {
                $content = file_get_contents($file);
                echo $content;
            }
            else {
                $this->returnEmptyJson();
            }
        }
    }
    
    /*
     * Returns empty json string
     */
    
    private function returnEmptyJson() {
        echo '[]';
    }
}
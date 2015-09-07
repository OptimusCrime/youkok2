<?php
/*
 * File: Setup.php
 * Holds: Set some initial content
 * Created: 19.11.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2;

use \Youkok2\Utilities\Database as Database;

require_once dirname(dirname(__FILE__)) . '/TestSettings.php';
require_once BASE_PATH . '/local.php';
require_once BASE_PATH . '/local-default.php';
require_once BASE_PATH . '/index.php';

class Setup {
    
    /*
     * Constructor
     */
    
    public function __construct() {
        // Insert users
        
        // Insert root element
        
        // Insert derp
    }
}

/*
 * Run the Setup
 */

new Setup();
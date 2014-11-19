<?php
/*
 * File: Harness.php
 * Holds: Some stuff to make Youkok2 testready
 * Created: 19.11.2014
 * Project: Youkok2
*/

namespace Youkok2;

use \Youkok2\Utilities\Database as Database;

/*
 * Include settings
 */

require_once dirname(__FILE__) . '/TestSettings.php';
require_once BASE_PATH . '/local.php';
require_once BASE_PATH . '/local-default.php';

/*
 * Include the bootstrap file
 */

require_once BASE_PATH . '/index.php';

/*
 * Harness class
*/

class Harness {
    
    /*
     * Constructor
     */
    
    public function __construct() {
        // Directories
        $this->createDirectories();
        
        // Database
        $this->databaseConnect();
        $this->databasePopulate();
    }
    
    /*
     * Create directories
     */
    
    private function createDirectories() {
        // Create cache directory
        if (!is_dir(CACHE_PATH)) {
            mkdir(CACHE_PATH);
        }
    }
    
    /*
     * Connect to database
     */
    
    private function databaseConnect() {
        Database::connect();
    }
    
    /*
     * Populate the database
     */
    
    private function databasePopulate() {
        // Check if there is a database file
        $db_dump = FILE_PATH . '/db.sql';
        if (file_exists($db_dump)) {
            // Create dummy database
            $content = file_get_contents($db_dump);
            
            // Run queries
            Database::$db->query($content);
        }
        else {
            // Missing dump
            echo "Missing database dump";
            
            // Kill
            die();
        }
    }
}

/*
 * Load class
 */

new Harness();
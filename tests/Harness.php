<?php
/*
 * File: Harness.php
 * Holds: Some stuff to make Youkok2 testready
 * Created: 19.11.2014
 * Project: Youkok2
*/

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
        $this->clearDatabase();
        
        // Migrations
        $this->migrateDatabase();
        
        // Finish
        echo "\n";
        echo "\033[32m╔═══════════════════════════════════════════════════════════════════╗\033[0m\n";
        echo "\033[32m║                                                                   ║\033[0m\n";
        echo "\033[32m║                            Running tests                          ║\033[0m\n";
        echo "\033[32m║                                                                   ║\033[0m\n";
        echo "\033[32m╚═══════════════════════════════════════════════════════════════════╝\033[0m\n";
        echo "\n\n";
    }
    
    /*
     * Create directories
     */
    
    private function createDirectories() {
        // Output
        echo "\n";
        echo "\033[32m╔═══════════════════════════════════════════════════════════════════╗\033[0m\n";
        echo "\033[32m║                                                                   ║\033[0m\n";
        echo "\033[32m║                         Creating directories                      ║\033[0m\n";
        echo "\033[32m║                                                                   ║\033[0m\n";
        echo "\033[32m╚═══════════════════════════════════════════════════════════════════╝\033[0m\n";
        echo "\n\n";
        
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
     * Clear the entire database
     */
    
    private function clearDatabase() {
        // Output
        echo "\033[32m╔═══════════════════════════════════════════════════════════════════╗\033[0m\n";
        echo "\033[32m║                                                                   ║\033[0m\n";
        echo "\033[32m║                        Clearing test database                     ║\033[0m\n";
        echo "\033[32m║                                                                   ║\033[0m\n";
        echo "\033[32m╚═══════════════════════════════════════════════════════════════════╝\033[0m\n";
        echo "\n\n";
        
        while (true) {
            $get_all_tables = "SHOW TABLES
            FROM youkok2_tests";
            
            $get_all_tables_query = Database::$db->query($get_all_tables);
            $tables = $get_all_tables_query->fetchAll();
            
            // Check if database is empty now
            if (count($tables) == 0) {
                break;
            }
            
            // Database is not empty, clear one more
            foreach ($tables as $k => $v) {
                $drop_table = 'DROP TABLE ' . $v[0];
                $drop_table_query = Database::$db->exec($drop_table);
            }
        }
    }
    
    /*
     * Run migrations on test database
     */
    
    private function migrateDatabase() {
        // Output
        echo "\033[32m╔═══════════════════════════════════════════════════════════════════╗\033[0m\n";
        echo "\033[32m║                                                                   ║\033[0m\n";
        echo "\033[32m║              Running migrations on test database                  ║\033[0m\n";
        echo "\033[32m║                                                                   ║\033[0m\n";
        echo "\033[32m╚═══════════════════════════════════════════════════════════════════╝\033[0m\n";
        echo "\n\n";
        
        // Running command
        exec('php vendor/bin/phinx migrate -e test', $output);
        
        // Outputting to console
        foreach ($output as $v) {
            echo $v . "\n";
        }
    }
}

/*
 * Load class
 */

new Harness();
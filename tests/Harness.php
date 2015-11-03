<?php
/*
 * File: Harness.php
 * Holds: Some stuff to make Youkok2 test ready
 * Created: 19.11.2014
 * Project: Youkok2
 *
 */

use \Youkok2\Youkok2 as Youkok2;
use \Youkok2\Models\User as User;
use \Youkok2\Utilities\Database as Database;

require_once dirname(__FILE__) . '/TestSettings.php';
require_once BASE_PATH . '/local.php';
require_once BASE_PATH . '/local-default.php';
require_once BASE_PATH . '/index.php';

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
        
        // Populate the database
        $this->populateDatabase();
        
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

        // Delete cache directory
        $dir = CACHE_PATH;
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            }
            else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);

        // Recreate directories
        @mkdir(CACHE_PATH);
        @mkdir(CACHE_PATH . '/elements/');
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
        
        // Drop foreign keys on drop table (for eazy sake)
        Database::$db->query('SET foreign_key_checks = 0');
        
        // Loop till all tables are dropped
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
        
        // Use foreign key constraints again
        Database::$db->query('SET foreign_key_checks = 1');
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
    
    /*
     * Populate the database with some database
     */
    
    private function populateDatabase() {
        $user = new User();
        $user->setEmail('foo@bar.com');
        $user->setPassword('123456789');
        $user->save();
    }
}

new Harness();
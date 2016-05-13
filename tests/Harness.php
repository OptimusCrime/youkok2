<?php
/*
 * File: Harness.php
 * Holds: Some stuff to make Youkok2 test ready
 * Created: 19.11.2014
 * Project: Youkok2
 *
 */

use Youkok2\Youkok2;
use Youkok2\Models\User;
use Youkok2\Utilities\Database;

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
        
        // Make sure to first create the file directories
        @mkdir(FILE_PATH . '/cache');
        
        // Make sure to unlink the database file
        @unlink(FILE_PATH . '/db.sqlite3');
        
        // Create file for the database
        file_put_contents(FILE_PATH . '/db.sqlite3', '');

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
        @mkdir(CACHE_PATH . '/youkok/');
        
        // Prettification
        echo "All done" . PHP_EOL . PHP_EOL;
    }
    
    /*
     * Connect to database
     */
    
    private function databaseConnect() {
        Database::connect();
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

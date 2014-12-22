<?php
/*
 * File: Database.php
 * Holds: Holds the database-connection in a static class
 * Created: 02.11.14
 * Project: Youkok2
*/

namespace Youkok2\Utilities;

/*
 * Simple class to abstract the database layer
 */

class Database {
    
    /*
     * Static variable that holds the database connection
     */
    
    public static $db;
    
    /*
     * Connect to database
     */
    
    public static function connect() {
        try {
            self::$db = new PDO2\PDO2(DATABASE_CONNECTION, DATABASE_USER, DATABASE_PASSWORD, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                \PDO::ATTR_ERRMODE, DATABASE_ERROR_MODE));
        }
        catch (\Exception $e) {
            throw new \Exception('Could not connect to the database');
        }
    }
    
    /*
     * Getters and setters for the database query log
     */
    
    public static function setLog(&$log) {
        self::$db->setLog($log);
    }
    public static function getLog() {
        return self::$db->getLog();
    }
    
    /*
     * Get number of queries
     */
    
    public static function getCount() {
        return self::$db->getCount();
    }
    
    /*
     * Close connection
     */
    
    public static function close() {
        self::$db = null;
    }
}
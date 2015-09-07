<?php
/*
 * File: Database.php
 * Holds: Holds the database-connection in a static class
 * Created: 02.11.14
 * Project: Youkok2
 * 
 */

namespace Youkok2\Utilities;

use \Youkok2\Utilities\BacktraceManager as BacktraceManager;

class Database {
    
    /*
     * Static variable that holds the database connection
     */
    
    public static $db;
    private $log;
    
    /*
     * Connect to database
     */
    
    public static function connect() {
        try {
            self::$db = new PDO2\PDO2(DATABASE_DNS . ';dbname=' . DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD, [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
            self::$db->setAttribute(\PDO::ATTR_ERRMODE, DATABASE_ERROR_MODE);
        }
        catch (\Exception $e) {
            throw new \Exception('Could not connect to the database');
        }
    }
    
    /*
     * Get number of queries
     */
    
    public static function getQueryCount() {
        return self::$db->getQueryCount();
    }
    
    /*
     * Get the backtrace
     */
    public static function getQueryBacktrace() {
        return BacktraceManager::cleanSqlLog(self::$db->getQueryLog());
    }
    
    /*
     * Close connection
     */
    
    public static function close() {
        self::$db = null;
    }
}
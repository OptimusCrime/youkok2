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
        self::$db = new PDO2\PDO2('mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_TABLE, DATABASE_USER, 
                                   DATABASE_PASSWORD, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    }
    
    /*
     * Getters and setters for the database query log
     */
    
    public static function setLog(&$log) {
        self::$db->setLog($log);
    }
    public static function getLog() {
        self::$db->getLog();
    }
    
    /*
     * Close connection
     */
    
    public static function close() {
        self::$db = null;
    }
}
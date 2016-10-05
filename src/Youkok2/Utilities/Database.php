<?php
namespace Youkok2\Utilities;

use Youkok2\Utilities\BacktraceManager;

class Database
{
    
    public static $db;
    private $log;
    
    public static function connect() {
        try {
            if (DATABASE_ADAPTER == 'mysql') {
                self::$db = new PDO2\PDO2(DATABASE_DNS . ';dbname=' . DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD, [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);
            }
            elseif (DATABASE_ADAPTER == 'sqlite') {
                self::$db = new PDO2\PDO2('sqlite:tests/files/db.sqlite3');
            }
            else {
                die('Not configured for this adapter');
            }
            
            self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // Handle profiling for adapters that are not sqlite
            if (DATABASE_ADAPTER != 'sqlite') {
                if (defined('PROFILING') and PROFILING) {
                    self::$db->query('SET profiling_history_size = 1000');
                    self::$db->query('SET profiling = 1');
                }
                
                if (defined('DEV') and DEV) {
                    self::$db->query('SET SESSION query_cache_type = 0');
                }
            }
        }
        catch (\Exception $e) {
            throw new \Exception('Could not connect to the database');
        }
    }
    
    public static function getQueryCount() {
        if (self::$db === null) {
            return null;
        }
        
        return self::$db->getQueryCount();
    }

    public static function getQueryBacktrace() {
        if (self::$db === null) {
            return null;
        }
        
        return BacktraceManager::cleanSqlLog(self::$db->getQueryLog());
    }
    
    public static function close() {
        self::$db = null;
    }
    
    public static function getProfilingDuration() {
        // Don't run this for tests
        if (DATABASE_ADAPTER == 'sqlite') {
            return 0;
        }

        $sum = 0;
        $get_profiles_query = self::$db->query('SHOW profiles');
        while ($row = $get_profiles_query->fetch(\PDO::FETCH_ASSOC)) {
            $sum += $row['Duration'];
        }
        
        return round(($sum * 1000), 4);
    }
    
    public static function getProfilingData() {
        // Don't run this for tests
        if (DATABASE_ADAPTER == 'sqlite') {
            return 0;
        }
        
        $data = [];
        
        $get_profiles_query = self::$db->query('SHOW profiles');
        while ($row = $get_profiles_query->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        
        return $data;
    }
}

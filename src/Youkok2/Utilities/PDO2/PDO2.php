<?php
/*
 * File: PDO2.php
 * Holds: Class that overrides the normal PDO-class to log queries etc
 * Created: 27.05.2014
 * Project: Youkok2
 * 
*/

namespace Youkok2\Utilities\PDO2;

require_once dirname(__FILE__) . '/PDOStatement2.php';

class PDO2 extends \PDO
{
    
    /*
     * Variables
     */
    
    private $queryCount;
    private $queryLog;
    
    /*
     * Constructor
     */
    
    public function __construct($dsn, $username = null, $password = null, $options = []) {
        // Call parent constructor first
        parent::__construct($dsn, $username, $password, $options);
        
        // Check if this is dev environment
        if (defined('DEV') and DEV) {
            $this->queryCount = 0;
            $this->queryLog = [];
            
            // Set logging to this query log
            $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, ['Youkok2\\Utilities\\PDO2\\PDOStatement2', [$this, &$this->queryLog]]);
        }
    }
    
    /*
     * Override PDO->query
     */
    
    public function query($query) {
        // Check if this is dev environment
        if (defined('DEV') and DEV) {
            // Increase the count
            $this->queryCount++;
            
            // Add to log
            $this->queryLog[] = ['query' => $query, 'backtrace' => debug_backtrace()];
        }
        
        // Call parent method
        return parent::query($query);
    }
    
    /*
     * Override PDO->exec
     */
    
    public function exec($statement) {
        // Check if this is dev environment
        if (defined('DEV') and DEV) {
            // Increase the count
            $this->queryCount++;
            
            // Add to log
            $this->queryLog[] = ['exec' => $statement, 'backtrace' => debug_backtrace()];
        }
        
        // Call parent method
        return parent::exec($statement);
    }
    
    /*
     * Override PDO->prepare
     */
    
    public function prepare($statement, $options = null) {
        // Check if this is dev environment
        if (defined('DEV') and DEV) {
            // Increase the count
            $this->queryCount++;
            
            // Add to log
            $this->queryLog[] = ['prepare' => $statement, 'backtrace' => debug_backtrace()];
        }
        
        // Call parent method
        return parent::prepare($statement);
    }
    
    /*
     * Return the number of queries here
     */
    
    public function getQueryCount() {
        // Returning the current count
        return $this->queryCount;
    }
    
    /*
     * Return the query log here
     */
    
    public function getQueryLog() {
        return $this->queryLog;
    }
}

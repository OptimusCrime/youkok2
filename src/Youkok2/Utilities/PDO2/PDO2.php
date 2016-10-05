<?php
namespace Youkok2\Utilities\PDO2;

class PDO2 extends \PDO
{
    
    private $queryCount;
    private $queryLog;
    
    
    public function __construct($dsn, $username = null, $password = null, $options = []) {
        parent::__construct($dsn, $username, $password, $options);

        if (defined('DEV') and DEV) {
            $this->queryCount = 0;
            $this->queryLog = [];
            
            // Set logging to this query log
            $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, ['Youkok2\\Utilities\\PDO2\\PDOStatement2', [
                $this,
                &$this->queryLog]]);
        }
    }
    
    public function query($query) {
        if (defined('DEV') and DEV) {
            $this->queryCount++;
            
            $this->queryLog[] = ['query' => $query, 'backtrace' => debug_backtrace()];
        }
        
        return parent::query($query);
    }
    
    public function exec($statement) {
        if (defined('DEV') and DEV) {
            $this->queryCount++;
            
            $this->queryLog[] = ['exec' => $statement, 'backtrace' => debug_backtrace()];
        }
        
        return parent::exec($statement);
    }
    
    public function prepare($statement, $options = null) {
        if (defined('DEV') and DEV) {
            $this->queryCount++;
            
            $this->queryLog[] = ['prepare' => $statement, 'backtrace' => debug_backtrace()];
        }
        
        return parent::prepare($statement);
    }
    
    public function getQueryCount() {
        return $this->queryCount;
    }
    
    public function getQueryLog() {
        return $this->queryLog;
    }
}

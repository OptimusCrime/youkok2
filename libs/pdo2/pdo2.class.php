<?php
/*
 * File: pdo2.class.php
 * Holds: Class that overrides the normal PDO-class to log queries etc
 * Created: 27.05.14
 * Last updated: 27.05.14
 * Project: Youkok2
 * 
*/

//
// The class
//

class PDO2 extends PDO {
    
    //
    // Variables
    //
    
    private $queryCount = 0;
    private $debug = false;
    protected $log;
    
    //
    // Set debug value
    //
    
    public function setLog(&$log) {
        $this->debug = true;
        $this->log = &$log;
        
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('PDOStatement2', array($this, &$log)));
    }
    
    //
    // pdo->query override
    //
    
    public function query($query) {
        // Increase counter
        $this->queryCount++;
        
        // Do logging?
        if ($this->debug) {
            $this->log[] = array('query' => $query);
        }
        
        // Call parent
        return parent::query($query);
    }
    
    //
    // pdo->exec override
    //
    
    public function exec($statement) {
        // Increase counter
        $this->queryCount++;
        
        // Do logging?
        if ($this->debug) {
            $this->log[] = array('exec' => $statement);
        }
        
        // Call parent
        return parent::exec($statement);
    }
    
    //
    // pdo->prepare
    //
    
    public function prepare($statement) {
        // Increase counter
        $this->queryCount++;
        
        // Do logging?
        if ($this->debug) {
            $this->log[] = array('prepare' => $statement);
        }
        
        // Call parent
        return parent::prepare($statement);
    }
    
    //
    // Public method for returning the number of queries ran
    //
    
    public function getCount() {
        // Returning the current count
        return $this->queryCount;
    }
}
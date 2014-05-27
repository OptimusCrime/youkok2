<?php
/*
 * File: pdostatement2.class.php
 * Holds: Class that overrides the normal PDO-class to log queries etc
 * Created: 27.05.14
 * Last updated: 27.05.14
 * Project: Youkok2
 * 
*/

//
// The class
//

class PDOStatement2 extends PDOStatement {

    //
    // Some variables
    //
    
    protected $connection;
	protected $bound_params = array();
    private $log;
	
    //
    // Override constructor
    //
    
	protected function __construct(PDO2 $connection, &$log) {
        $this->connection = $connection;
        $this->log = &$log;
	}
    
    //
    // Override
    //
    
    public function execute($arr = null) {
        if ($arr == null) {
            parent::execute();
        }
        else {
            $this->log[] = array('execute' => $arr, 'backtrace' => debug_backtrace());
            parent::execute($arr);
        }
    }
}
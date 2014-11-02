<?php
/*
 * File: pdostatement2.class.php
 * Holds: Class that overrides the normal PDO-class to log queries etc
 * Created: 27.05.14
 * Project: Youkok2
*/

namespace Youkok2\Utilities\PDO2;

class PDOStatement2 extends \PDOStatement {

    //
    // Some variables
    //
    
    protected $connection;
    protected $bound_params = array();
    private $log;

    //
    // Override constructor
    //
    
    protected function __construct($connection, &$log) {
        $this->connection = $connection;
        $this->log = &$log;
    }
    
    //
    // Override
    //
    
    public function execute($arr = null) {
        if ($arr == null) {
            $this->log[] = array('execute' => [], 'backtrace' => debug_backtrace());
            parent::execute();
        }
        else {
            $this->log[] = array('execute' => $arr, 'backtrace' => debug_backtrace());
            parent::execute($arr);
        }
    }
}
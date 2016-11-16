<?php
namespace Youkok2\Utilities\PDO2;

class PDOStatement2 extends \PDOStatement
{
    
    protected $connection;
    protected $bound_params = [];
    private $log;
    
    protected function __construct($connection, &$log) {
        $this->connection = $connection;
        $this->log = &$log;
    }
    
    public function execute($arr = null) {
        if ($arr == null) {
            $this->log[] = ['execute' => [], 'backtrace' => debug_backtrace()];
            parent::execute();
        }
        else {
            $this->log[] = ['execute' => $arr, 'backtrace' => debug_backtrace()];
            parent::execute($arr);
        }
    }
}

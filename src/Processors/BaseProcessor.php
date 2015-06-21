<?php
/*
 * File: Base.php
 * Holds: Base processor class
 * Created: 056.12.14
 * Project: Youkok2
*/

namespace Youkok2\Processors;

/*
 * Define what classes to use
 */

use \Youkok2\Youkok2 as Youkok2;
use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\Database as Database;

/*
 * Class that all the processors extends
 */

class BaseProcessor extends Youkok2 {
    
    /*
     * Variable for storing data
     */
    
    private $data;
    private $climate;
    protected $outputData;
    protected $returnData;
    protected $mode;
    
    /*
     * Constructor
     */

    public function __construct($outputData = false, $returnData = false) {
        $this->outputData = $outputData;
        $this->returnData = $returnData;
        $this->data = [];
        $this->climate = new \League\CLImate\CLImate;
        
        if (!$this->requireCli()) {
            $this->mode = 'buffer';
        }
        else {
            $this->mode = 'cli';
        }
    }
    
    /*
     * Setters and getters for data
     */
    
    protected function setData($key, $data) {
        $this->data[$key] = $data;
    }
    protected function getData($key) {
        return $this->data[$key];
    }
    
    /*
     * Output data
     */
    
    public function outputData() {
        // Close db
        $this->closeConnection();
        
        // Output content
        if (php_sapi_name() !== 'cli') {
            // Echo JSON content here
            echo json_encode($this->data);
        }
        else {
            // Return to console using CLImate
            $this->climate->json($this->data);
        }
    }
    
    /*
     * Return data
     */
    
    public function returnData() {
        // Return data
        return $this->data;
    }
    
    /*
     * No access
     */
    
    protected function noAccess() {
        $this->setData('code', 500);
        $this->setData('msg', 'No access');
    }
    
    /*
     * Set error
     */
    
    protected function setError() {
        $this->setData('code', 500);
        $this->setData('msg', 'Something went wrong');
    }
    
    /*
     * Require only one kind of request etc
     */
    
    protected static function requireCli() {
        return php_sapi_name() == 'cli';
    }
    protected static function requireAdmin() {
        // Check if need to connect to database first
        if (Database::$db === null) {
            // Connect to database
            Database::connect();
        }
        
        // Init user
        Me::init();
        
        // Do the check
        return (Me::isLoggedIn() and Me::isAdmin());
    }

    /*
     * Check if we can connect to the database
     */

    protected function makeDatabaseConnection() {
        // Check if already connected
        if (Database::$db !== null) {
            return true;
        }

        // Not connected, try
        try {
            Database::connect();
            return true;
        }
        catch (Exception $e) {
            $this->setData('code', 500);
            $this->setData('msg', 'Could not connect to database');

            return false;
        }
    }
    
    /*
     * Close database (if open)
     */
    
    public function closeConnection() {
        if (Database::$db !== null) {
            Database::close();
        }
    }
}
<?php
/*
 * File: Base.php
 * Holds: Base processor class
 * Created: 056.12.2014
 * Project: Youkok2
*/

namespace Youkok2\Processors;

use Youkok2\Models\Me;
use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Database;
use Youkok2\Views\Processors;

abstract class BaseProcessor extends Processors {
    
    /*
     * Variable for storing data
     */
    
    private $method;
    private $data;
    
        
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
        
        // Set data to empty array
        $this->data = [];
    }

    public function setMethod($method) {
        if ($method === null) {
            $this->method = 'run';
        }
        else {
            $this->method = $method;
        }
    }
    
    public function execute() {  
        parent::run();

        // Check if user needs database access
        if ($this->requireDatabase()) {
            // Try to connect to database
            $this->makeDatabaseConnection();
        }
        
        // Check if we could try to log the user in
        if ($this->canBeLoggedIn()) {
            Me::init();
        }

        // Check if user has access
        if (!$this->checkPermissions()) {
            // User does not have access to this view
            $this->setData('code', 500);
            $this->setData('msg', 'No access');
        }

        // Run the class method
        $this->{$this->method}();

        // Handle output
        $this->handleOutput();
    }

    /*
     * Check if user has the correct permissions
     */

    protected function checkPermissions() {
        return true;
    }

    /*
     * Different types of permissions
     */
    protected function requireCli() {
        return php_sapi_name() == 'cli';
    }
    protected function requireAdmin() {
        // Check if database is initiated
        if (Database::$db === null) {
            if (!$this->makeDatabaseConnection()) {
                return false;
            }
        }

        // Init user is not already inited
        Me::init();

        // Check if the user is admin
        return Me::isAdmin();
    }
    protected function requireLoggedIn() {
        // Check if database is initiated
        if (Database::$db === null) {
            if (!$this->makeDatabaseConnection()) {
                return false;
            }
        }

        // Init user is not already inited
        Me::init();

        // Check if the user is admin
        return Me::isLoggedIn();
    }

    /*
     * Checks if the user needs connection with the database
     */

    protected function requireDatabase() {
        return false;
    }
    
    /*
     * If this is set to true we can try to log the user in, but it is not required
     */

    protected function canBeLoggedIn() {
        return false;
    }

    /*
     * If the data should be encoded, the encoding of the entire data object is done in this method
     */

    protected function encodeData($data) {
        return $data;
    }
    
    /*
     * Setters and getters for data
     */
    
    protected function setData($key, $data) {
        $this->data[$key] = $data;
    }
    protected function setAllData($data) {
        $this->data = $data;
    }
    public function getData() {
        // Store data in new variable
        $return_data = $this->data;

        // Check if we should encode
        if ($this->getSetting('encode') === null or $this->getSetting('encode')) {
            $return_data = $this->encodeData($return_data);
        }

        // Return the corect data
        return $return_data;
    }
    
    /*
     * Output data
     */
    
    protected function handleOutput() {
        // About to output, make sure cachemanager is storing everything
        CacheManager::store();
        
        // Check if we should close the database
        if ($this->getSetting('close_db') and $this->getSetting('close_db')) {
            // Close connection
            $this->closeConnection();
        }
        
        $output_data = $this->data;
        if ($this->getSetting('encode') === null or $this->getSetting('encode')) {
            $output_data = $this->encodeData($output_data);
        }
        
        // Check if we can access the application
        if ($this->getSetting('application')) {
            $this->application->setBody(json_encode($output_data));
        }
        else {
            // Not an application, check if we are calling from CLI
            if (php_sapi_name() == 'cli') {
                // CLI output using CLImate
                $climate = new \League\CLImate\CLImate;
                $climate->json($output_data);
            }
        }
        
        return $output_data;
    }
    
    /*
     * Set error
     */
    
    protected function setError() {
        $this->setData('code', 500);
        $this->setData('msg', 'Something went wrong');
    }

    /*
     * Set ok
     */

    protected function setOk() {
        $this->setData('code', 200);
        $this->setData('msg', 'OK');
    }

    /*
     * Connect to the database
     */

    private function makeDatabaseConnection() {
        // Make sure we don't already have a database connection running
        if (Database::$db !== null) {
            return true;
        }
        
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
    
    private function closeConnection() {
        if (Database::$db !== null) {
            Database::close();
        }
    }
}
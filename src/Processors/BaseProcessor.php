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

abstract class BaseProcessor extends Youkok2 {
    
    /*
     * Variable for storing data
     */
    
    private $data;
    private $noOutput;
    
    /*
     * Constructor
     */

    public function __construct($method, $noOutput = false) {
        // Set data to empty array
        $this->data = [];

        // Set value of noOutput
        $this->noOutput = $noOutput;

        // Check if user needs database access
        if ($this->requireDatabase()) {
            // Try to connect to database
            if (!$this->makeDatabaseConnection()) {
                // Handle output
                return $this->handleOutput();
            }
        }

        // Check if user has access
        if (!$this->checkPermissions()) {
            // User does not have access to this view
            $this->setData('code', 500);
            $this->setData('msg', 'No access');

            // Handle output
            return $this->handleOutput();

        }
        else {
            // Run the method
            call_user_func_array([$this, $method], []);

            // Handle output
            return $this->handleOutput();
        }

    }

    /*
     * Check if user has the correct permissions
     */

    protected function checkPermissions() {
        return true;
    }

    /*
     * Checks if the user needs connection with the database
     */

    protected function requireDatabase() {
        return false;
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
    
    protected function handleOutput() {
        // Check if we should output data at all
        if (!$this->noOutput) {
            // Handle CLI and JSON
            if (php_sapi_name() == 'cli') {
                // CLI output using CLImate
                $climate = new \League\CLImate\CLImate;
                $climate->json($this->data);
            }
            else {
                // Simply echo as JSON content
                echo json_encode($this->data);
            }
        }

        // Return the data
        return $this->data;
    }
    
    /*
     * Set error
     */
    
    protected function setError() {
        $this->setData('code', 500);
        $this->setData('msg', 'Something went wrong');
    }

    /*
     * Connect to the database
     */

    private function makeDatabaseConnection() {
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
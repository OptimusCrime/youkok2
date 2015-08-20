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
    private $settings;
    
    /*
     * Constructor
     */

    public function __construct($method, $settings) {
        // Set data to empty array
        $this->data = [];

        // Store settings
        $this->settings = $settings;

        // Check if user needs database access
        if ($this->requireDatabase()) {
            // Try to connect to database
            if (!$this->makeDatabaseConnection()) {
                // Handle output
                $this->handleOutput();
            }
        }

        // Check if user has access
        if (!$this->checkPermissions()) {
            // User does not have access to this view
            $this->setData('code', 500);
            $this->setData('msg', 'No access');

            // Handle output
            $this->handleOutput();

        }
        else {
            // Run the method
            call_user_func_array([$this, $method], []);

            // Handle output
            $this->handleOutput();
        }

        // Check if we should close the database
        if (isset($this->settings['close_db']) and $this->settings['close_db']) {
            // Close connection
            $this->closeConnection();
        }
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
    
    /*
     * Output data
     */
    
    protected function handleOutput() {
        // Check if we should output data at all
        if (isset($this->settings['output']) and $this->settings['output']) {
            $output_data = $this->data;

            // Check if we should encode
            if (isset($this->settings['encode']) and $this->settings['encode']) {
                $output_data = $this->encodeData($output_data);
            }

            // Handle CLI and JSON
            if (php_sapi_name() == 'cli') {
                // CLI output using CLImate
                $climate = new \League\CLImate\CLImate;
                $climate->json($output_data);
            }
            else {
                // Simply echo as JSON content
                echo json_encode($output_data);
            }
        }
    }

    /*
     * Return data
     */

    public function getData() {
        // Store data in new variable
        $return_data = $this->data;

        // Check if we should encode
        if (isset($this->settings['encode']) and $this->settings['encode']) {
            $return_data = $this->encodeData($return_data);
        }

        // Return the corect data
        return $return_data;
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
    
    /*
     * Get settings
     */
    
    protected function getSetting($key) {
        if (!isset($this->settings[$key])) {
            return null;
        }
        
        return $this->settings[$key];
    }
    
    /*
     * Set settings
     */
    
    protected function setSetting($key, $value) {
        $this->settings[$key] = $value;
    }
}
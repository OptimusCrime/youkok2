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

/*
 * Class that all the processors extends
 */

class Base extends Youkok2 {
    
    /*
     * Variable for storing data
     */
    
    private $data;
    private $returnData;
    private $climate;
    
    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        $this->returnData = $returnData;
        $this->data = [];
        $this->climate = new \League\CLImate\CLImate;
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
     * Return data
     */
    
    protected function returnData() {
        // Check if we should return or output
        if ($this->returnData) {
            // Return
            return $this->data;
        }
        else {
            // Output the data
            if (php_sapi_name() !== 'cli') {
                // Return as json
                echo json_encode($this->data);
            }
            else {
                // Return to console using CLImate
                $this->climate->json($this->data);
            }
        }
    }
    
    /*
     * No access
     */
    
    protected function noAccess() {
        $this->setData('code', 500);
        $this->setData('msg', 'No access');
    }
    
    /*
     * Require only one kind of request etc
     */
    
    protected static function requireCli() {
        return php_sapi_name() == 'cli';
    }
    protected static function requireAdmin() {
        return (Me::isLoggedIn() and Me::isAdmin());
    }
}
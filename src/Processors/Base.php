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

/*
 * Class that all the processors extends
 */

class Base extends Youkok2 {
    
    /*
     * Variable for storing data
     */
    
    private $data;
    
    /*
     * Constructor
     */

    public function __construct() {
        $this->data = array();
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
        if (php_sapi_name() !== 'cli') {
            // Return as json
            echo json_encode($this->data);
        }
        else {
            // Display nicely TODO
            print_r($this->data);
        }
    }
}
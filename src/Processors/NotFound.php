<?php
/*
 * File: NotFound.php
 * Holds: Returns error because processor was not found
 * Created: 02.10.13
 * Project: Youkok2
 * 
*/

namespace Youkok2\Processors;

/*
 * The NotFound class, extending Base class
 */

class NotFound extends Base {

    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        // Calling Base' constructor
        parent::__construct($returnData);
        
        // Set data
        $this->setData('msg', 'Processor not found');
        $this->setData('code', 500);
        
        // Return data
        $this->returnData();
    }
}
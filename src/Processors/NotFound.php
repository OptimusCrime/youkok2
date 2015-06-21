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

class NotFound extends BaseProcessor {

    /*
     * Constructor
     */

    public function __construct($outputData = false, $returnData = false) {
        // Calling Base' constructor
        parent::__construct($outputData, $returnData);
        
        // Set data
        $this->setData('msg', 'Processor not found');
        $this->setData('code', 500);
        
        // Handle output
        if ($this->outputData) {
            $this->outputData();
        }
        if ($this->returnData) {
            return $this->returnData();
        }
    }
}
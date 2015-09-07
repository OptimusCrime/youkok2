<?php
/*
 * File: NotFound.php
 * Holds: Returns error because processor was not found
 * Created: 02.10.2013
 * Project: Youkok2
 * 
 */

namespace Youkok2\Processors;

class NotFound extends BaseProcessor {

    /*
     * Constructor
     */

    public function __construct($method, $settings) {
        // Calling Base' constructor
        parent::__construct($method, $settings);
    }

    /*
     * Just to have a function to call or else the abstract class is angry
     */

    protected function run() {
        // Set data
        $this->setData('msg', 'Processor not found');
        $this->setData('code', 500);

    }
}
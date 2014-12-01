<?php
/*
 * File: Youkok2.php
 * Holds: The definite base class for the entire system
 * Created: 01.11.14
 * Project: Youkok2
 * 
*/

namespace Youkok2;

/*
 * The base class Youkok2
 */

class Youkok2 {
    
    /*
     * Run a processor with a given action
     */
    
    public static function runProcessor($action) {
        echo 'Running processor ' . $action;
    }
    
}
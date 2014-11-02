<?php
/*
 * File: external.controller.php
 * Holds: The FlatController-class
 * Created: 18.09.14
 * Project: Youkok2
 * 
*/

//
// The ExternalController class
//

class ExternalController extends Youkok2 {

    //
    // The constructor for this subclass
    //

    public function __construct($routes) {
        // Calling Base' constructor
        parent::__construct($routes);
    }
}

//
// Return the class name
//

return 'ExternalController';
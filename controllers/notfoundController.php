<?php
/*
 * File: notfoundController.php
 * Holds: The NotfoundController-class that returns 404 error-message to the user
 * Created: 02.10.13
 * Last updated: 02.10.13
 * Project: Youkok2
 * 
*/

//
// The REST-class doing most of the magic
//

class NotfoundController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();
        
        echo '<h1>404: Page not found...</h1>';
    }
}

//
// Loading the class-name dynamically and creating an instance doing our magic
//

// Getting the current file-path
$path = explode('/',__FILE__);

// Including the run-script to execute it all
include_once "run.php";
?>
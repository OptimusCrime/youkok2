<?php
/*
 * File: profileController.php
 * Holds: The ProfileController-class
 * Created: 02.10.13
 * Last updated: 05.12.13
 * Project: Youkok2
 * 
*/

//
// The REST-class doing most of the magic
//

class ProfileController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths) {
        // Calling Base' constructor
        parent::__construct($paths);
        
        echo 'My profile goes here';
    }
}

//
// Loading the class-name dynamically and creating an instance doing our magic
//

// Getting the current file-path
$path = explode('/', __FILE__);

// Including the run-script to execute it all
include_once 'run.php';
?>
<?php
/*
 * File: notfound.controller.php
 * Holds: The NotfoundController-class that returns 404 error-message to the user
 * Created: 02.10.13
 * Project: Youkok2
 * 
*/

//
// NotfoundController handles 404
//

class NotfoundController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);
        
        // Set 404 header
        header('HTTP/1.0 404 Not Found');
        
        // Null the menu
        $this->template->assign('HEADER_MENU', null);

        // Display template
        $this->template->assign('SITE_TITLE', 'Siden ble ikke funnet');
        $this->displayAndCleanup('404.tpl');
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
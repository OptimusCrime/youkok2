<?php
/*
 * File: other.controller.php
 * Holds: The OtherController-class for misc-stuff
 * Created: 02.10.13
 * Project: Youkok2
 * 
*/

//
// The OtherController class. Handles different dynamic pages
//

class OtherController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);
        
        // Checking what to call
        if ($_GET['q'] == 'wall-of-shame') {
            $this->wallOfShame();
        }
        else {
            // 404
            $this->display404();
        }
    }

    //
    // Method for displaying Wall of Shame
    //

    private function wallOfShame() {
        // Set menu
        $this->template->assign('HEADER_MENU', 'WOS');

        // Display template
        $this->template->assign('SITE_TITLE', 'Wall of Shame');
        $this->displayAndCleanup('other_wall_of_shame.tpl');
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
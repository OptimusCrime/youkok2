<?php
/*
 * File: otherController.php
 * Holds: The OtherController-class for misc-stuff
 * Created: 02.10.13
 * Last updated: 12.04.14
 * Project: Youkok2
 * 
*/

//
// The REST-class doing most of the magic
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
        $this->template->display('other_wall_of_shame.tpl');
    }

    //
    // Logg out
    //

    private function logOut() {
        echo "okei";
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
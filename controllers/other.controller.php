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

class OtherController extends Youkok2 {

    //
    // The constructor for this subclass
    //

    public function __construct($routes) {
        // Calling Base' constructor
        parent::__construct($routes);
        
        // Checking what to call
        if ($this->queryGet(0) == 'wall-of-shame') {
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
// Return the class name
//

return 'OtherController';
?>
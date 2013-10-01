<?php
/*
 * File: homeController.php
 * Holds: The HomeController-class
 * Created: 02.10.13
 * Last updated: 02.10.13
 * Project: Youkok2
 * 
*/

//
// The REST-class doing most of the magic
//

class HomeController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();
        
        // Testing
        $this->template->assign('OUTPUT','this is the home-screen');
        $this->template->display('index.tpl');
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
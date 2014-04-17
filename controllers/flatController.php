<?php
/*
 * File: flatController.php
 * Holds: The FlatController-class
 * Created: 02.10.13
 * Last updated: 15.04.14
 * Project: Youkok2
 * 
*/

//
// The FlatController class
//

class FlatController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);
        
        // Check query
        if ($_GET['q'] == 'om') {
        	$this->template->assign('HEADER_MENU', 'ABOUT');
        	$this->displayAndCleanup('flat_about.tpl');
        }
        elseif ($_GET['q'] == 'retningslinjer') {
        	$this->template->assign('HEADER_MENU', null);
        	$this->displayAndCleanup('flat_retningslinjer.tpl');
        }
        elseif ($_GET['q'] == 'privacy') {
            $this->template->assign('HEADER_MENU', null);
            $this->displayAndCleanup('flat_privacy.tpl');
        }
        elseif ($_GET['q'] == 'hjelp') {
            $this->template->assign('HEADER_MENU', null);
            $this->displayAndCleanup('flat_help.tpl');
        }
        else {
            // Page was not found
        	$this->display404();
        }
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
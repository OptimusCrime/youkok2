<?php
/*
 * File: flatController.php
 * Holds: The FlatController-class
 * Created: 02.10.13
 * Last updated: 05.12.13
 * Project: Youkok2
 * 
*/

//
// The REST-class doing most of the magic
//

class FlatController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);
        
        if (str_replace('/', '', $_GET['q']) == 'om') {
        	$this->template->assign('HEADER_MENU', 'ABOUT');
        	$this->template->display('flat_about.tpl');
        }
        elseif (str_replace('/', '', $_GET['q']) == 'retningslinjer') {
        	$this->template->assign('HEADER_MENU', null);
        	$this->template->display('flat_retningslinjer.tpl');
        }
        elseif (str_replace('/', '', $_GET['q']) == 'privacy') {
            $this->template->assign('HEADER_MENU', null);
            $this->template->display('flat_privacy.tpl');
        }
        else {
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
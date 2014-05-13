<?php
/*
 * File: error.controller.php
 * Holds: The ErrorController-class
 * Created: 02.05.14
 * Last updated: 13.05.14
 * Project: Youkok2
 * 
*/

//
// In case something goes very wrong
//

class ErrorController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base, $reason) {
        // Calling Base' constructor
        parent::__construct($paths, $base, true);

        if ($reason == 'db') {
            $this->template->assign('SITE_TITLE', 'Noe gikk galt');
            $this->template->display('error_db.tpl');
        }
        else {
            // Dette er ikke implementert enda
            $this->template->assign('SITE_TITLE', 'Noe gikk galt');
            $this->template->display('error_generic.tpl');
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
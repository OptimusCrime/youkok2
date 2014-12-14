<?php
/*
 * File: error.controller.php
 * Holds: The ErrorController-class
 * Created: 02.05.14
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

    public function __construct($routes, $reason) {
        // Calling Base' constructor
        parent::__construct($routes, true);

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
// Return the class name
//

return 'ErrorController';
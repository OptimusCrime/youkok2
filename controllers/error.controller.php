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

class ErrorController extends Youkok2 {

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
// Return the class name
//

return 'ErrorController';
?>
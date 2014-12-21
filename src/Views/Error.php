<?php
/*
 * File: Error.php
 * Holds: Displaying an error message to the user
 * Created: 02.05.14
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

/*
 * The Home class, extending Base class
 */

class Error extends Base {

    /*
     * Constructor
     */

    public function __construct($reason) {
        // Calling Base' constructor
        parent::__construct(true);

        if ($reason == 'db') {
            $this->template->assign('SITE_TITLE', 'Noe gikk galt');
            $this->template->display('error_db.tpl');
        }
        elseif ($reason == 'offline') {
            // Dette er ikke implementert enda
            $this->template->assign('SITE_TITLE', 'Youkok2 er offline');
            $this->template->display('error_offline.tpl');
        }
        else {
            // Dette er ikke implementert enda
            $this->template->assign('SITE_TITLE', 'Noe gikk galt');
            $this->template->display('error_generic.tpl');
        }
    }
}
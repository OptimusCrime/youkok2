<?php
/*
 * File: Error.php
 * Holds: Displaying an error message to the user
 * Created: 02.05.2014
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

/*
 * The Error class, extending BaseView
 */

class Error extends BaseView {

    /*
     * Constructor
     */

    public function __construct($reason) {
        // Calling Base' constructor
        parent::__construct(true);

        // Check error reason
        if ($reason == 'db') {
            // No database connection
            $this->template->assign('SITE_TITLE', 'Noe gikk galt');
            $this->template->display('error_db.tpl');
        }
        elseif ($reason == 'offline') {
            // Application is offline
            $this->template->assign('SITE_TITLE', 'Youkok2 er offline');
            $this->template->display('error_offline.tpl');
        }
        else {
            // Some other error
            $this->template->assign('SITE_TITLE', 'Noe gikk galt');
            $this->template->display('error_generic.tpl');
        }
    }
}
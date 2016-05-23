<?php
/*
 * File: Error.php
 * Holds: Displaying an error message to the user
 * Created: 02.05.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views;

class Error extends BaseView
{
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Run the view
     */

    public function run() {
        
        // Check error reason
        if ($this->getSetting('reason') === 'db') {
            // Set error code
            $this->application->setStatus(503);

            // No database connection
            $this->template->assign('SITE_TITLE', 'Noe gikk galt');
            $this->application->setBody($this->template->fetch('error_db.tpl'));
        }
        elseif ($this->getSetting('reason') === 'unavailable') {
            // Set error code
            $this->application->setStatus(503);

            // Application is offline
            $this->template->assign('SITE_TITLE', 'Youkok2 er ikke tilgjengelig');
            $this->application->setBody($this->template->fetch('error_unavailable.tpl'));
        }
        else {
            // Some other error
            $this->template->assign('SITE_TITLE', 'Noe gikk galt');
            $this->application->setBody($this->template->fetch('error_generic.tpl'));
        }
    }
}

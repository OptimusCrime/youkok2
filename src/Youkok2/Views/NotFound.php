<?php
/*
 * File: NotFound.php
 * Holds: Returns 404 error if the view was not found
 * Created: 02.10.2013
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

class NotFound extends BaseView {
    
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
        
        // Make sure to kill the view if something broke
        if ($this->getSetting('kill') === true) {
            return;
        }
        
        // Set 404 header
        $this->application->setStatus(404);
        
        // Null the menu
        $this->template->assign('HEADER_MENU', null);
        
        // Turn on caching
        $this->template->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);

        // Display template
        $this->template->assign('SITE_TITLE', 'Siden ble ikke funnet');
        $this->displayAndCleanup('404.tpl');
    }
}
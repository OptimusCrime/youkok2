<?php
/*
 * File: notfound.controller.php
 * Holds: The NotfoundController-class that returns 404 error-message to the user
 * Created: 02.10.13
 * Project: Youkok2
 * 
*/

//
// NotfoundController handles 404
//

class NotfoundController extends Youkok2 {

    //
    // The constructor for this subclass
    //

    public function __construct($routes) {
        // Calling Base' constructor
        parent::__construct($routes);
        
        // Set 404 header
        header('HTTP/1.0 404 Not Found');
        
        // Null the menu
        $this->template->assign('HEADER_MENU', null);
        
        // Turn on caching
        $this->template->setCaching(Smarty::CACHING_LIFETIME_CURRENT);

        // Display template
        $this->template->assign('SITE_TITLE', 'Siden ble ikke funnet');
        $this->displayAndCleanup('404.tpl');
    }
}

//
// Return the class name
//

return 'NotFoundController';
<?php
/*
 * File: redirect.controller.php
 * Holds: The RedirectController-class
 * Created: 11.09.14
 * Project: Youkok2
 * 
*/

//
// The RedirectController class
//

class RedirectController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($routes) {
        // Calling Base' constructor
        parent::__construct($routes);
        
        // Check query
        if ($this->queryGetSize() > 0) {
            $id = $this->queryGet($this->queryGetSize() - 1);
            if (is_numeric($id)) {
                // Check if real object
                $item = new Item($this);
                $item->createById($id);
                
                if ($item->wasFound() and $item->isLink()) {
                    // All good, check if we should count or ignore redirect
                    if (!isset($_GET['donotlogthisdownload'])) {
                        // Log download
                        $item->addDownload();
                    }
                    
                    // Redirect
                    header('Location: ' . $item->getUrl());
                    
                    // Kill
                    exit();
                }
            }
        }

        // If we got this far, something is fucked up!
        $this->display404();
    }
}

//
// Return the class name
//

return 'RedirectController';
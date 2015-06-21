<?php
/*
 * File: Redirect.php
 * Holds: The Redirect class
 * Created: 11.09.14
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Element as Element;

/*
 * The Redrict class, extending Base class
 */

class Redirect extends BaseView {

    //
    // The constructor for this subclass
    //

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();
        
        // Check query
        if ($this->queryGetSize() > 0) {
            $id = $this->queryGet($this->queryGetSize() - 1);
            if (is_numeric($id)) {
                $element = new Element();
                $element->createById($id);
                
                // Check if everything is good
                if ($element->controller->wasFound() and $element->isLink()) {
                    // All good, check if we should count or ignore redirect
                    if (!isset($_GET['donotlogthisdownload'])) {
                        // Log download
                        $element->controller->addDownload();
                    }
                    
                    // Redirect
                    header('Location: ' . $element->getUrl());
                    
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
<?php
/*
 * File: Redirect.php
 * Holds: Handles redirecting links
 * Created: 11.09.2014
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Utilities\Loader;

class Redirect extends BaseView {

    /*
     * Constructor
     */

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();
        
        // Check query
        if (Loader::queryGetSize() > 0) {
            $id = Loader::queryGet(Loader::queryGetSize() - 1);
            if (is_numeric($id)) {
                $element = Element::get($id);
                
                // Check if everything is good
                if ($element->wasFound() and $element->isLink()) {
                    // All good, check if we should count or ignore redirect
                    if (!isset($_GET['donotlogthisdownload'])) {
                        // Log download
                        $element->addDownload();
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
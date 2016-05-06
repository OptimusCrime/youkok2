<?php
/*
 * File: StaticFiles.php
 * Holds: Displays static files
 * Created: 25.05.2015
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

use Youkok2\Utilities\Loader;

class StaticFiles extends BaseView {
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Different static files
     */

    public function returnChangelog() {
        // Get changelog content
        $content = file_get_contents(BASE_PATH . '/files/changelog.md');
        
        // Assign content to placeholder
        $this->template->assign('CHANGELOG_CONTENT', $content);
        
        // Set headers (to fix unicode fuckup)
        $this->application->setHeader('Content-Type', 'text/plain; charset=utf-8');
        
        // Render
        $this->displayAndCleanup('changelog.tpl');
    }
    public function returnFavicon() {
        // Get the right file
        $file = Loader::queryGet(0);
        
        // Open the file
        $name = BASE_PATH . '/files/' . $file;
        $fp = fopen($name, 'rb');

        // Send the correct content type
        if ($file == 'favicon.png') {
            $this->application->setHeader('Content-Type', 'image/png');
        }
        else {
            $this->application->setHeader('Content-Type', 'image/x-icon');
        }
        
        // Send the right headers
        header('Content-Length: ' . filesize($name));

        // Dump the picture and stop the script
        fpassthru($fp);
        exit;
    }
}
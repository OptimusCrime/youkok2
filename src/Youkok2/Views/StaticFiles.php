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

class StaticFiles extends BaseView
{
    
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
        // Find the file name
        $path_split = explode('/', $this->path);
        $filename = '';
        foreach ($path_split as $v) {
            if (strlen($v) > 0) {
                $filename = $v;
                break;
            }
        }
        
        // Get the file
        $file = BASE_PATH . '/files/' . $filename;
        
        // Send the right headers
        $this->application->setHeader('Content-Length', filesize($file));
        
        // Read content of file
        $this->application->addStream(file_get_contents($file));

        // Send the correct content type
        if ($file == 'favicon.png') {
            $this->application->setHeader('Content-Type', 'image/png');
        }
        else {
            $this->application->setHeader('Content-Type', 'image/x-icon');
        }
    }
}

<?php
/*
 * File: flat.controller.php
 * Holds: The FlatController-class
 * Created: 02.10.13
 * Project: Youkok2
 * 
*/

//
// The FlatController class
//

class FlatController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);
        
        // Check query
        if ($this->queryGet(0) == 'om') {
            // Turn on caching
            $this->template->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
            
            // Assign header and title
        	$this->template->assign('HEADER_MENU', 'ABOUT');
            $this->template->assign('SITE_TITLE', 'Om Youkok2');
            
            // Display
        	$this->displayAndCleanup('flat_about.tpl');
        }
        elseif ($this->queryGet(0) == 'retningslinjer') {
            // Assign header and title
        	$this->template->assign('HEADER_MENU', null);
            $this->template->assign('SITE_TITLE', 'Retningslinjer for Youkok2');
            
            // Fix list for filtypes
            $mime_types = explode(',', SITE_ACCEPTED_FILETYPES);
            $mime_types_string = '';
            foreach ($mime_types as $v) {
                $mime_types_string .= '<li>' . $v . '</li>';
            }
            $this->template->assign('SITE_ACCEPTED_FILETYPES', $mime_types_string);
            
            // Fix list for filendings
            $endings = explode(',', SITE_ACCEPTED_FILEENDINGS);
            $endings_string = '';
            foreach ($endings as $v) {
                $endings_string .= '<li>.' . $v . '</li>';
            }
            $this->template->assign('SITE_ACCEPTED_FILEENDINGS', $endings_string);
            
            // Display the page
        	$this->displayAndCleanup('flat_retningslinjer.tpl');
        }
        elseif ($this->queryGet(0) == 'privacy') {
            // Turn on caching
            $this->template->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
            
            // Assign header and title
            $this->template->assign('HEADER_MENU', null);
            $this->template->assign('SITE_TITLE', 'Privacy');
            
            // Display the page
            $this->displayAndCleanup('flat_privacy.tpl');
        }
        elseif ($this->queryGet(0) == 'hjelp') {
            // Turn on caching
            $this->template->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
            
            // Assign header and title
            $this->template->assign('HEADER_MENU', 'HELP');
            $this->template->assign('SITE_TITLE', 'Hjelp');
            
            // Display the page
            $this->displayAndCleanup('flat_help.tpl');
        }
        elseif ($this->queryGet(0) == 'karma') {
            // Turn on caching
            $this->template->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
            
            // Assign header and title
            $this->template->assign('HEADER_MENU', null);
            $this->template->assign('SITE_TITLE', 'Karma');
            
            // Display the page
            $this->displayAndCleanup('flat_karma.tpl');
        }
        elseif ($this->queryGet(0) == 'changelog.txt') {
            // Get changelog content
            $content = file_get_contents($this->basePath . '/files/changelog.md');
            
            // Assign content to placeholder
            $this->template->assign('CHANGELOG_CONTENT', $content);
            
            // Set headers (to fix unicode fuckup)
            header('Content-Type: text/plain; charset=utf-8');
            
            // Render
            $this->displayAndCleanup('changelog.tpl');
        }
        else {
            // Page was not found
        	$this->display404();
        }
    }
}

//
// Loading the class-name dynamically and creating an instance doing our magic
//

// Getting the current file-path
$path = explode('/', __FILE__);

// Including the run-script to execute it all
include_once 'run.php';
?>
<?php
/*
 * File: Flat.php
 * Holds: Class for displaying flat files
 * Created: 02.10.2013
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

/*
 * The Flat class, extending BaseView
 */

class Flat extends BaseView {

    /*
     * Constructor
     */

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();
    }
    
    /*
     * Different view functions
     */
    
    public function displayAbout() {
        // Turn on caching
        $this->template->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);
        
        // Assign header and title
        $this->template->assign('HEADER_MENU', 'ABOUT');
        $this->template->assign('SITE_TITLE', 'Om Youkok2');
        
        // Display
        $this->displayAndCleanup('flat_about.tpl');
    }
    public function displayTerms() {
        // Assign header and title
        $this->template->assign('HEADER_MENU', null);
        $this->template->assign('SITE_TITLE', 'Retningslinjer for Youkok2');
        
        // Fix list for filendings
        $file_endings = explode(',', ACCEPTED_FILEENDINGS);
        $this->template->assign('ACCEPTED_FILEENDINGS', $file_endings);
        
        // Display the page
        $this->displayAndCleanup('flat_retningslinjer.tpl');
    }
    public function displayHelp() {
        // Turn on caching
        $this->template->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);
        
        // Assign header and title
        $this->template->assign('HEADER_MENU', 'HELP');
        $this->template->assign('SITE_TITLE', 'Hjelp');
        
        // Display the page
        $this->displayAndCleanup('flat_help.tpl');
    }
    public function displayKarma() {
        // Turn on caching
        $this->template->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);
        
        // Assign header and title
        $this->template->assign('HEADER_MENU', null);
        $this->template->assign('SITE_TITLE', 'Karma');
        
        // Display the page
        $this->displayAndCleanup('flat_karma.tpl');
    }
}
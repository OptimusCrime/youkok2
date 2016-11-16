<?php
namespace Youkok2\Views;

class Flat extends BaseView
{
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function displayAbout() {
        $this->template->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);
        
        $this->template->assign('HEADER_MENU', 'ABOUT');
        $this->template->assign('SITE_TITLE', 'Om Youkok2');
        
        $this->displayAndCleanup('flat_about.tpl');
    }
    public function displayTerms() {
        $this->template->assign('HEADER_MENU', null);
        $this->template->assign('SITE_TITLE', 'Retningslinjer for Youkok2');
        
        $file_endings = explode(',', ACCEPTED_FILEENDINGS);
        $this->template->assign('ACCEPTED_FILEENDINGS', $file_endings);
        
        $this->displayAndCleanup('flat_retningslinjer.tpl');
    }
    public function displayHelp() {
        $this->template->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);
        
        $this->template->assign('HEADER_MENU', 'HELP');
        $this->template->assign('SITE_TITLE', 'Hjelp');
        
        $this->displayAndCleanup('flat_help.tpl');
    }
}

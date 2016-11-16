<?php
namespace Youkok2\Views;

class NotFound extends BaseView
{
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        
        // Make sure to kill the view if something broke
        if ($this->getSetting('kill') === true) {
            return;
        }
        
        $this->application->setStatus(404);
        
        $this->template->assign('HEADER_MENU', null);
        $this->template->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);

        $this->template->assign('SITE_TITLE', 'Siden ble ikke funnet');
        $this->displayAndCleanup('404.tpl');
    }
}

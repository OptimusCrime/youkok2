<?php
namespace Youkok2\Views;

class Error extends BaseView
{
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        
        if ($this->getSetting('reason') === 'db') {
            $this->application->setStatus(503);

            $this->template->assign('SITE_TITLE', 'Noe gikk galt');
            $this->application->setBody($this->template->fetch('error_db.tpl'));
        }
        elseif ($this->getSetting('reason') === 'unavailable') {
            $this->application->setStatus(503);

            $this->template->assign('SITE_TITLE', 'Youkok2 er ikke tilgjengelig');
            $this->application->setBody($this->template->fetch('error_unavailable.tpl'));
        }
        else {
            $this->application->setStatus(500);

            $this->template->assign('SITE_TITLE', 'Noe gikk galt');
            $this->application->setBody($this->template->fetch('error_generic.tpl'));
        }
    }
}

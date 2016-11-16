<?php
namespace Youkok2\Views;

use Youkok2\Utilities\Loader;

class StaticFiles extends BaseView
{
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function returnChangelog() {
        $content = file_get_contents(BASE_PATH . '/files/changelog.md');
        
        $this->template->assign('CHANGELOG_CONTENT', $content);
        
        $this->application->setHeader('Content-Type', 'text/plain; charset=utf-8');
        
        $this->displayAndCleanup('changelog.tpl');
    }
    public function returnFavicon() {
        $path_split = explode('/', $this->path);
        $filename = '';
        foreach ($path_split as $v) {
            if (strlen($v) > 0) {
                $filename = $v;
                break;
            }
        }
        
        $file = BASE_PATH . '/files/' . $filename;
        
        $this->application->setHeader('Content-Length', filesize($file));
        
        $this->application->addStream(file_get_contents($file));
        
        if ($filename == 'favicon.png') {
            $this->application->setHeader('Content-Type', 'image/png');
        }
        else {
            $this->application->setHeader('Content-Type', 'image/x-icon');
        }
    }
}

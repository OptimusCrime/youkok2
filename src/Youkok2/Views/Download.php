<?php
namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Models\Controllers\Cache\MeDownloadsController;
use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Loader;

class Download extends BaseView
{
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        $should_display_404 = false;
        
        $element = Element::get($this->path);

        if ($element->wasFound() and !$element->isPending() and !$element->isDeleted()) {
            $file_location = $element->getPhysicalLocation();

            if (!$element->isDirectory()) {
                if (file_exists($file_location)) {
                    // Check if wget
                    if (!isset($_SERVER['HTTP_USER_AGENT']) or
                        strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'wget') !== false) {
                        // Wget attempt, serve false file
                        $this->loadFileDownload(BASE_PATH . '/files/wget_file.txt', $element->getName());
                        exit;
                    }

                    if (!isset($_GET['donotlogthisdownload'])) {
                        $element->addDownload($this->me);
                        
                        if ($this->me->isLoggedIn()) {
                            CacheManager::deleteCache($this->me->getId(), MeDownloadsController::$cacheKey);
                        }
                    }

                    // Check if we should return fake http response to facebook crawler
                    if (strpos($_SERVER['HTTP_USER_AGENT'], 'facebook') !== false) {
                        // Facebook crawler
                        $this->template->assign('DOWNLOAD_FILE', $element->getName());
                        $this->template->assign('DOWNLOAD_SIZE', $element->getSize(true));
                        $this->template->assign('DOWNLOAD_URL', $_SERVER['REQUEST_URI']);

                        $download_parent = '';
                        $root_parent = $element->controller->getRootParent();
                        if ($root_parent != null) {
                            $course = $root_parent->controller->getCourse();
                            $download_parent .= $course['code'] . ' - ' . $course['name'];
                        }

                        $this->template->assign('DOWNLOAD_PARENT', $download_parent);

                        $this->displayAndCleanup('download.tpl');
                    }
                    else {
                        $this->close();

                        // File exists, check if we should display or directly download
                        $display = false;
                        $display_instead = explode(',', DISPLAY_INSTEAD_OF_DOWNLOAD);
                        foreach ($display_instead as $v) {
                            if ($v == $element->getMimeType()) {
                                $display = true;
                                break;
                            }
                        }

                        if ($display) {
                            $this->loadFileDisplay($file_location, $element->getName());
                        }
                        else {
                            $this->loadFileDownload($file_location, $element->getName());
                        }
                    }
                }
                else {
                    $should_display_404 = true;
                }
            }
        }
        else {
            $should_display_404 = true;
        }

        if ($should_display_404) {
            $this->display404();
        }
    }
    
    private function loadFileDisplay($file, $name) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_info = finfo_file($finfo, $file);
        
        $this->application->setHeader('Content-Type', $file_info);
        $this->application->setHeader('Content-Disposition', 'inline; filename="' . $name . '"');
        $this->application->setHeader('Expires', '0');
        $this->application->setHeader('Cache-Control', 'must-revalidate');
        $this->application->setHeader('Pragma', 'public');
        $this->application->setHeader('Content-Length', filesize($file));
        
        $this->application->addStream(file_get_contents($file));
    }
    
    private function loadFileDownload($file, $name) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_info = finfo_file($finfo, $file);
        
        $this->application->setHeader('Content-Description', 'File Transfer');
        $this->application->setHeader('Content-Type', $file_info);
        $this->application->setHeader('Content-Disposition', 'attachment; filename="' . $name . '"');
        $this->application->setHeader('Content-Transfer-Encoding', 'binary');
        $this->application->setHeader('Expires', '0');
        $this->application->setHeader('Cache-Control', 'must-revalidate');
        $this->application->setHeader('Pragm', 'public');
        $this->application->setHeader('Content-Length', filesize($file));
        
        $this->application->addStream(file_get_contents($file));
    }
}

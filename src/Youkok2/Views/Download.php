<?php
/*
 * File: Download.php
 * Holds: Handling for downloading files
 * Created: 02.10.2013
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Models\Controllers\Cache\MeDownloadsController;
use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Loader;

class Download extends BaseView {

    /*
     * Constructor
     */

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();
        
        // Displaying 404 or not
        $should_display_404 = false;
        
        // Create new object
        $element = Element::get(Loader::queryGetClean());

        // Check if was found or invalid url
        if ($element->wasFound() and !$element->isPending() and !$element->isDeleted()) {
            $file_location = $element->getPhysicalLocation();

            // Check if zip download or not
            if (!$element->isDirectory()) {
                if (file_exists($file_location)) {
                    // Check if wget
                    if (!isset($_SERVER['HTTP_USER_AGENT']) or strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'wget') !== false) {
                        // Wget attempt, serve false file
                        $this->loadFileDownload(BASE_PATH . '/files/wget_file.txt', $element->getName());
                        exit;
                    }

                    // Check if we should log download
                    if (!isset($_GET['donotlogthisdownload'])) {
                        // Log download
                        $element->addDownload();
                        
                        // Check if the current user is logged in
                        if (Me::isLoggedIn()) {
                            // Clear the cache for the MeDownloads element
                            CacheManager::deleteCache(Me::getId(), MeDownloadsController::$cacheKey);
                        }
                    }

                    // Check if we should return fake http response to facebook crawler
                    if (strpos($_SERVER['HTTP_USER_AGENT'], 'facebook') !== false) {

                        // Facebook crawler
                        $this->template->assign('DOWNLOAD_FILE', $element->getName());
                        $this->template->assign('DOWNLOAD_SIZE', $element->getSize(true));
                        $this->template->assign('DOWNLOAD_URL', $_SERVER['REQUEST_URI']);

                        // Get item parent information
                        $download_parent = '';
                        $root_parent = $element->controller->getRootParent();
                        if ($root_parent != null) {
                            $course = $root_parent->controller->getCourse();
                            $download_parent .= $course['code'] . ' - ' . $course['name'];
                        }

                        $this->template->assign('DOWNLOAD_PARENT', $download_parent);

                        // Display fake http response for Facebook
                        $this->displayAndCleanup('download.tpl');
                    }
                    else {
                        // Close database connection
                        $this->close();

                        // File exists, check if we should display or directly download
                        $display = false;
                        $display_instead = explode(',', DISPLAY_INSTEAD_OF_DOWNLOAD);
                        foreach ($display_instead as $v) {
                            if ($v == $element->getMimeType()) {
                                // Display
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
                    // File was not found, wtf
                    $should_display_404 = true;
                }
            }
        }
        else {
            // File is not visible
            $should_display_404 = true;
        }

        // Check if we should display 404 or not
        if ($should_display_404) {
            $this->display404();
            die();
        }
    }
    
    /*
     * Loading an actual file for displaying
     */
    
    private function loadFileDisplay($file, $name) {
        // Fetch mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_info = finfo_file($finfo, $file);
        
        // Set header options;
        header('Content-Type: ' . $file_info);
        header('Content-Disposition: inline; filename="' . $name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        
        // Clean ob and flush
        ob_clean();
        flush();
        
        // Read content of file
        readfile($file);
        
        // Exit the program
        exit;
    }

    /*
     * Loading an actual file for downloading
     */
    
    private function loadFileDownload($file, $name) {
        // Fetch mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_info = finfo_file($finfo, $file);
        
        // Set header options
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $file_info);
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        
        // Clean ob and flush
        ob_clean();
        flush();
        
        // Read content of file
        readfile($file);
        
        // Exit the program
        exit;
    }
}
<?php
/*
 * File: download.controller.php
 * Holds: The DownloadController-class
 * Created: 02.10.13
 * Project: Youkok2
 * 
*/

//
// The DownloadController class
//

class DownloadController extends Youkok2 {

    //
    // The constructor for this subclass
    //

    public function __construct($routes) {
        // Calling Base' constructor
        parent::__construct($routes);
        
        // Displaying 404 or not
        $should_display_404 = false;

        // Create new object
        $item = new Item($this);
        $item->setLoadFullLocation(true);
        $item->setShouldLoadRoot(true);
        $item->createByUrl($this->queryGetClean());

        // Check if was found or invalid url
        if ($item->wasFound()) {
            // Check if visible
            if ($item->isVisible()) {
                $file_location = FILE_ROOT . '/'. $item->getFullLocation();

                if (file_exists($file_location)) {
                    // Check if we should log download
                    if (!isset($_GET['donotlogthisdownload'])) {
                        // Logg download
                        $item->addDownload();
                    }

                    // Check if we should return fake http response to facebook crawler
                    if (strpos($_SERVER['HTTP_USER_AGENT'], 'facebook') !== false) {
                        // Facebook crawler
                        $this->template->assign('DOWNLOAD_FILE', $item->getName());
                        $this->template->assign('DOWNLOAD_SIZE', $item->getSizePretty());
                        $this->template->assign('DOWNLOAD_URL', $_SERVER['REQUEST_URI']);

                        // Get item parent information
                        $download_parent = '';
                        $root_parent = $item->getRootParent();
                        if ($item->getParent() != $root_parent->getId()) {
                            $local_dir_element = $this->collection->get($item->getParent());
                            $download_parent = $local_dir_element->getName() . ', ';
                        }
                        if ($root_parent != null) {
                            $download_parent .= $root_parent->getName() . ' ';
                        }

                        $this->template->assign('DOWNLOAD_PARENT', $download_parent);

                        // Display fake http response for Facebook
                        $this->displayAndCleanup('download.tpl');
                    }
                    else {
                        // Close database connection
                        $this->close();

                        // File exists, download!
                        $this->loadFile($file_location, $item->getName());
                    }
                }
                else {
                    // File was not found, wtf
                    $should_display_404 = true;
                }
            }
            else {
                // File is not visible
                $should_display_404 = true;
            }
        }
        else {
            // File is not visible
            $should_display_404 = true;
        }

        // Check if we should display 404 or not
        if ($should_display_404) {
            $this->display404();
        }
    } 
    
    //
    // Loading an actual file from the fileserver
    //
    
    private function loadFile($file, $name) {
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

//
// Return the class name
//

return 'DownloadController';
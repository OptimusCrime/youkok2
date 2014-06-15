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
        $item = new Item($this->collection, $this->db);
        $item->setLoadFullLocation(true);
        $item->createByUrl($this->queryGetClean());

        // Check if was found or invalid url
        if ($item->wasFound()) {
            // Item was found, store id
            $element_id = $item->getId();
            
            // Add to collection if new
            $this->collection->add($item);

            // Fetch back
            $element = $this->collection->get($element_id);

            // Just for safty
            if ($element == null) {
                // This should in theory never happen...
                $should_display_404 = true;
            }
            else {
                // Check if visible
                if ($element->isVisible()) {
                    $file_location = $this->fileDirectory . '/'. $element->getFullLocation();
                    if (file_exists($file_location)) {
                        // Check if we should log download
                        if (!isset($_GET['donotlogthisdownload'])) {
                            // Logg download
                            $element->addDownload($this->user);
                        }

                        // Close database connection
                        $this->close();

                        // File exists, download!
                        $this->loadFile($file_location, $element->getName());
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
        }
        else {
            // Retarded url
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
?>
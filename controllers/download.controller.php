<?php
/*
 * File: download.controller.php
 * Holds: The DownloadController-class
 * Created: 02.10.13
 * Last updated: 18.05.14
 * Project: Youkok2
 * 
*/

//
// The DownloadController class
//

class DownloadController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);
        
        // Displaying 404 or not
        $should_display_404 = false;

        // Getting the path
        if (!isset($_GET['q'])) {
            // Not sure if this is even possible?
            $should_display_404 = true;
        }
        else {
            // Create new object
            $item = new Item($this->collection, $this->db);
            $item->setShouldLoadPhysicalLocation(true);
            $item->createByUrl($_GET['q']);

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
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        
        ob_clean();
        flush();
        
        readfile($file);
        
        exit;
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
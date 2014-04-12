<?php
/*
 * File: downloadController.php
 * Holds: The DownloadController-class
 * Created: 02.10.13
 * Last updated: 05.12.13
 * Project: Youkok2
 * 
*/

//
// The REST-class doing most of the magic
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
                $this->collection->addIfDoesNotExist($item);

                // Fetch back
                $element = $this->collection->get($element_id);

                // Just for safty
                if ($element == null) {
                    // This should in theory never happen...
                    $should_display_404 = true;
                }
                else {
                    $file_location = $this->fileDirectory . '/'. $element->getFullLocation();

                    if (file_exists($file_location)) {
                        // Logg download
                        $element->addDownload($this->user);
                        
                        // File exists, download!
                        $this->loadFile($file_location);
                    }
                    else {
                        // File was not found, wtf
                        echo "File is missing!";
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
    
    private function loadFile($file) {
        // Todo, update download-count, check if user is logged in, the increase user download too
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
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
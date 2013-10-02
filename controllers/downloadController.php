<?php
/*
 * File: downloadController.php
 * Holds: The DownloadController-class
 * Created: 02.10.13
 * Last updated: 02.10.13
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

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();
        
        // Parse the correct path
        $file_location = $this->getFileRealLocation();
        
        // Check if it returned a real file or not
        if (!$file_location) {
            // Did not return a real file
            $this->return404();
        }
        else {
            // Did return a real file
        }
        
        // http://php.net/manual/en/function.readfile.php
    }
    
    //
    //
    //
    
    private function getFileRealLocation() {
        // Getting the path
        if (!isset($_GET['q'])) {
            return false;
        }
        else {
            // Reverse the url
            $file = $this->reverseUrl($_GET['q']);
            
            // Check if the file exists or not
            if (1 == 1) {
                // File exists, buffer and load the file
                $this->loadFile($file);
            }
            else {
                // File does not exists, log error and return 404
                $this->return404();
            }
        }
    }
    
    //
    //
    //
    
    private function return404() {
        return '<h1>File not found...</h1>';
    }
    
    //
    //
    //
    
    private function loadFile($file) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
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
include_once "run.php";
?>
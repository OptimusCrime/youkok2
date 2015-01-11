<?php
/*
 * File: Download.php
 * Holds: Downloading one or a collection of elements
 * Created: 02.10.13
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;
/*
 * Define what classes to use
 */

use \Youkok2\Models\Element as Element;

/*
 * The Download class, extending Youkok2 base class
 */

class Download extends Base {

    /*
     * Constructor
     */

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();
        
        // Displaying 404 or not
        $should_display_404 = false;
        
        // Create new object
        $element = new Element();
        $element->createByUrl($this->queryGetClean());
        
        // Check if was found or invalid url
        if ($element->controller->wasFound()) {
            // Check if visible
            if ($element->isVisible()) {
                $file_location = $element->controller->getPhysicalLocation();
                
                // Check if zip download or not
                if ($element->isDirectory()) {
                    // TODO
                    /*
                    if (is_dir($file_location)) {
                        // Get direct children, that are elements
                        $files = $item->getChildren(Item::$file);
                        
                        // Check if any children were returned
                        if (count($files) > 0) {
                            // Create array with locations
                            $files_archive = array();
                            foreach ($files as $v) {
                                $files_archive[] = array('file' => FILE_PATH . $v->getFullLocation(),
                                                         'name' => $v->getName());
                            }
                            
                            
                            
                            // Create zip archive
                            $file = tempnam(FILE_PATH . '/tmp', 'zip');
                            
                            $zip = new ZipArchive;
                            $res = $zip->open($file, ZipArchive::CREATE);
                            foreach ($files_archive as $v) {
                                if (file_exists($v['file'])) {
                                    $zip->addFile($v['file'], $v['name']);
                                }
                            }
                            $zip->close();
                            
                            // Do download
                            header('Content-Description: File Transfer');
                            header('Content-Transfer-Encoding: binary');
                            header('Expires: 0');
                            header('Cache-Control: must-revalidate');
                            header('Pragma: public');
                            header('Content-Type: application/zip');
                            header('Content-Disposition: attachment; filename="arkiv.zip"');
                            header('Content-Length: ' . filesize($file));
                            
                            // Clean ob and flush
                            ob_clean();
                            flush();
                            
                            // Read the contents
                            readfile($file);
                            
                            // Delete file
                            //unlink($file);
                        }
                        else {
                            // No content found
                            $should_display_404 = true;
                        }
                    }
                    else {
                        // Is not a directory
                        $should_display_404 = true;
                    }
                    */
                }
                else {
                    if (file_exists($file_location)) {
                        // Check if we should log download
                        if (!isset($_GET['donotlogthisdownload'])) {
                            // Log download
                            //$item->addDownload();
                        }
    
                        // Check if we should return fake http response to facebook crawler
                        if (isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'facebook') !== false) {
                            // TODO
                            /*
                            // Facebook crawler
                            $this->template->assign('DOWNLOAD_FILE', $element->getName());
                            $this->template->assign('DOWNLOAD_SIZE', $element->getSizePretty());
                            $this->template->assign('DOWNLOAD_URL', $_SERVER['REQUEST_URI']);
    
                            // Get item parent information
                            $download_parent = '';
                            $root_parent = $element->getRootParent();
                            if ($element->getParent() != $root_parent->getId()) {
                                $local_dir_element = $this->collection->get($element->getParent());
                                $download_parent = $local_dir_element->getName() . ', ';
                            }
                            if ($root_parent != null) {
                                $download_parent .= $root_parent->getName() . ' ';
                            }
                            
                            $this->template->assign('DOWNLOAD_PARENT', $download_parent);
                            
                            // Display fake http response for Facebook
                            $this->displayAndCleanup('download.tpl');
                            */
                        }
                        else {
                            // Close database connection
                            $this->close();
                            
                            // File exists, download!
                            $this->loadFile($file_location, $element->getName());
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
<?php
/*
 * File: CreateFile.php
 * Holds: Upload one or multiple files
 * Created: 22.02.15
 * Project: Youkok2
 * 
*/

namespace Youkok2\Processors;

/*
 * Define what classes to use
 */

use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Models\Element as Element;
use \Youkok2\Models\History as History;
use \Youkok2\Models\Karma as Karma;
use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\MessageManager as MessageManager;
use \Youkok2\Utilities\Utilities as Utilities;

/*
 * The Static class, extending Base class
 */

class CreateFile extends Base {

    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        // Calling Base' constructor
        parent::__construct($returnData);
        
        // Check database
        if ($this->makeDatabaseConnection()) {
            // Init user
            Me::init();
            
            // Check if online
            if (!Me::isLoggedIn() or (Me::isLoggedIn() and Me::canContribute())) {
                $this->process();
            }
            else {
                $this->setError();
            }
        }
        else {
            $this->setError();
        }
        
        // Return data
        $this->returnData();
    }
    
    /*
     * Process upload
     */
    
    private function process() {
        $request_ok = false;
        
        // Check parent
        if (isset($_GET['parent']) and is_numeric($_GET['parent'])) {
            $parent = ElementCollection::get($_GET['parent']);

            // Check if valid Element
            if ($parent !== null) {
                // Check if any files was sent
                if (isset($_FILES['files']) and count($_FILES['files']) > 0) {
                    // Check file type
                    $file_type_split = explode('.', $_FILES['files']['name'][0]);
                    $file_type = $file_type_split[count($file_type_split) - 1];
                    
                    // Get allowed filetypes
                    $allowed_filetypes = explode(',', ACCEPTED_FILEENDINGS);
                    
                    // Check if .zip
                    if (strtolower($file_type) == 'zip') {
                        // TODO
                        /*
                        // Create random folder for the zip archive
                        $tmp_dir = FILE_ROOT . '/tmp/' . substr(md5(rand(0, 1000000)), 0, 15) . time() . '/';
                        mkdir($tmp_dir);
                        
                        // Extract
                        $zipArchive = new ZipArchive();
                        $result = $zipArchive->open($_FILES['files']['tmp_name'][0]);
                        if ($result) {
                            $zipArchive->extractTo($tmp_dir);
                            $zipArchive->close();
                        }
                        
                        // Get content of archive
                        $iter = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($tmp_dir, RecursiveDirectoryIterator::SKIP_DOTS),
                                RecursiveIteratorIterator::SELF_FIRST,
                                RecursiveIteratorIterator::CATCH_GET_CHILD
                        );

                        $zip_archive = array();
                        foreach ($iter as $path => $dir) {
                            if (!$dir->isDir()) {
                                $zip_archive[] = $path;
                            }
                        }
                        
                        // Analyze content
                        foreach ($zip_archive as $v) {
                            if ($can_be_added) {
                                $zip_archive_file_split = explode('.', $v);
                                
                                if (!in_array($zip_archive_file_split[count($zip_archive_file_split) - 1], $allowed_filetypes) or 
                                    $zip_archive_file_split[count($zip_archive_file_split) - 1] == 'zip') {
                                    // This file is not allowed!
                                    $can_be_added = false;
                                    
                                    // Add error message
                                    $this->addMessage('\'' . $_FILES['files']['name'][0] . '\' kunne ikke lastes opp fordi den inneholder fil(er) av typer som ikke er godkjent.', 'danger');
                                    die($zip_archive_file_split[count($zip_archive_file_split) - 1]);
                                    // Break
                                    break;
                                }
                            }
                        }
                        
                        // Remove folder
                        $iter2 = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($tmp_dir, RecursiveDirectoryIterator::SKIP_DOTS),
                        RecursiveIteratorIterator::CHILD_FIRST
                        );

                        foreach ($iter2 as $fileinfo) {
                            $remove_op = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                            $remove_op($fileinfo->getRealPath());
                        }

                        rmdir($tmp_dir);
                        */
                    }
                    else {
                        // Check filetype
                        if (in_array($file_type, $allowed_filetypes)) {
                            // Valid request
                            $request_ok = true;
                        }
                    }
                }
            }
        }
        
        // Check if we are good to go
        if ($request_ok) {
            // Get file name
            unset($file_type_split[count($file_type_split) - 1]);
            $file_name = implode('.', $file_type_split);
            
            // Check duplicates for url friendly
            $letters = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
            $url_friendly = Utilities::urlSafe($file_name, true);
            while (true) {
                $get_duplicate = "SELECT id
                FROM archive 
                WHERE parent = :id
                AND url_friendly = :url_friendly";
                
                $get_duplicate_query = Database::$db->prepare($get_duplicate);
                $get_duplicate_query->execute(array(':id' => $parent->getId(),
                    ':url_friendly' => $url_friendly));
                $row_duplicate = $get_duplicate_query->fetch(\PDO::FETCH_ASSOC);
                if (isset($row_duplicate['id'])) {
                    $url_friendly = Utilities::urlSafe($letters[rand(0, count($letters) - 1)] . $url_friendly);
                }
                else {
                    // Gogog
                    break;
                }
            }
            
            // Test for missing image
            $mime_type_pretty = str_replace('/', '_', $_FILES['files']['type'][0]);
            if (file_exists(BASE_PATH . '/assets/images/icons/' . $mime_type_pretty . '.png')) {
                $has_missing_image = 0;
            }
            else {
                $has_missing_image = 1;
            }
            
            // Get checksum
            $checksum = md5_file($_FILES['files']['tmp_name'][0]) . '.' . $file_type;
            
            // Set information
            $element = new Element();
            $element->setName($file_name . '.' . $file_type);
            $element->setUrlFriendly($url_friendly);
            $element->setChecksum($checksum);
            $element->setParent($parent->getId());
            $element->setMimeType($mime_type_pretty);
            $element->setMissingImage($has_missing_image);
            $element->setSize($_FILES['files']['size'][0]);
            
            // Check if we should auto hide the element
            if (!Me::isLoggedIn()) {
                $element->setVisible(false);
            }
            else {
                // User is logged in, set owner
                $element->setOwner(Me::getId());
            }
            
            // Save element
            $element->save();
            
            // Get parent directory and create the sub directories if they are not already there
            $parent_dir = FILE_PATH . '/' . substr($checksum, 0, 1);
            if (!file_exists($parent_dir)) {
                mkdir($parent_dir);
            }
            
            // Second parent dir
            $parent_dir .= '/' . substr($checksum, 1, 1);
            if (!file_exists($parent_dir)) {
                mkdir($parent_dir);
            }
            
            // Move the file
            move_uploaded_file($_FILES['files']['tmp_name'][0], $parent_dir . '/' . $checksum);
            
            // Check if parent was sat to empty and if we should update that
            if (Me::isLoggedIn() and $parent->isEmpty()) {
                $parent->setEmpty(false);
                $parent->update();
                
                // Clear cache on parent
                $parent->controller->deleteCache();
            }
            
            // Add message
            MessageManager::addFileMessage($file_name . '.' . $file_type);
            
            // Check if logged in
            if (Me::isLoggedIn()) {
                // Add history element
                $history = new History();
                $history->setUser(Me::getId());
                $history->setFile($element->getId());
                $history->save();
                
                // Add karma
                $karma = new Karma();
                $karma->setUser(Me::getId());
                $karma->setFile($element->getId());
                $karma->save();
                
                // Add karma to user
                Me::increaseKarmaPending(5);
                Me::update();
            }
            
            // Send successful code
            $this->setData('code', 200);
        }
        else {
            $this->setData('code', 500);
        }
    }
}
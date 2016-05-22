<?php
/*
 * File: CreateFile.php
 * Holds: Upload one or multiple files
 * Created: 22.02.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Processors;

use Youkok2\Models\Element;
use Youkok2\Models\History;
use Youkok2\Models\Karma;
use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\MessageManager;
use Youkok2\Utilities\Utilities;

class CreateFile extends BaseProcessor
{
    
    /*
     * Override
     */

    protected function canBeLoggedIn() {
        return true;
    }
    
    /*
     * Override
     */

    protected function requireDatabase() {
        return true;
    }
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Process upload
     */
    
    public function run() {
        $request_ok = false;
        
        // Check parent
        if (isset($_GET['parent']) and is_numeric($_GET['parent'])) {
            $parent = Element::get($_GET['parent']);

            // Check if valid Element
            if ($parent !== null) {
                // Check if any files was sent
                if (isset($_FILES['files']) and count($_FILES['files']) > 0) {
                    // Check file type
                    $file_type_split = explode('.', $_FILES['files']['name'][0]);
                    $file_type = $file_type_split[count($file_type_split) - 1];
                    
                    // Get allowed filetypes
                    $allowed_filetypes = explode(',', ACCEPTED_FILEENDINGS);
                    
                    // Check filetype
                    if (in_array($file_type, $allowed_filetypes)) {
                        // Valid request
                        $request_ok = true;
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
            $num = 2;
            $url_friendly_base = Utilities::urlSafe($file_name);
            $url_friendly = Utilities::urlSafe($file_name);
            
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
                    // Generate new url friendly
                    $url_friendly = Utilities::urlSafe($url_friendly_base . '-' . $num . '.' . $file_type);
                    
                    // Increase num
                    $num++;
                }
                else {
                    // Gogog
                    break;
                }
            }
            
            // Check duplicates for names
            $num = 2;
            $name_base = $file_name;
            $name = $file_name . '.' . $file_type;
            
            // Loop 'till no collides
            while (true) {
                $get_duplicate2  = "SELECT id" . PHP_EOL;
                $get_duplicate2 .= "FROM archive" . PHP_EOL; 
                $get_duplicate2 .= "WHERE parent = :id" . PHP_EOL;
                $get_duplicate2 .= "AND name = :name";
                
                $get_duplicate2_query = Database::$db->prepare($get_duplicate2);
                $get_duplicate2_query->execute(array(':id' => $parent->getId(),
                    ':name' => $name));
                $row_duplicate2 = $get_duplicate2_query->fetch(\PDO::FETCH_ASSOC);
                
                // Check if any url patterns collide
                if (isset($row_duplicate2['id'])) {
                    // Generate new url friendly
                    $name = $name_base . ' (' . $num . ')' . '.' . $file_type;
                    
                    // Increase num
                    $num++;
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
            $element->setName($name);
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
                $parent->deleteCache();
            }
            
            // Add message
            MessageManager::addFileMessage($this->application, $file_name . '.' . $file_type);
            
            // Check if logged in
            if (Me::isLoggedIn()) {
                // Add history element
                $history = new History();
                $history->setUser(Me::getId());
                $history->setFile($element->getId());
                $history->setHistoryText('%u lastet opp ' . $element->getName());
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

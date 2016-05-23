<?php
/*
 * File: CreateFolder.php
 * Holds: Create a new link
 * Created: 25.02.2015
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

class CreateFolder extends BaseProcessor
{
    
    /*
     * Override
     */

    protected function checkPermissions() {
        return $this->requireLoggedIn();
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
        if (isset($_POST['id']) and is_numeric($_POST['id'])) {
            $parent = Element::get($_POST['id']);

            // Check if valid Element
            if ($parent->wasFound()) {
                // Check if name was sent
                if (isset($_POST['name']) and strlen($_POST['name']) >= 4) {
                    $request_ok = true;
                }
            }
        }
        
        // Check if we are good to go
        if ($request_ok) {
            // Check duplicates for url friendly
            $num = 2;
            $url_friendly_base = Utilities::urlSafe($_POST['name']);
            $url_friendly = Utilities::urlSafe($_POST['name']);
            
            // Loop 'till no collides
            while (true) {
                $get_duplicate  = "SELECT id" . PHP_EOL;
                $get_duplicate .= "FROM archive" . PHP_EOL;
                $get_duplicate .= "WHERE parent = :id" . PHP_EOL;
                $get_duplicate .= "AND url_friendly = :url_friendly";
                
                $get_duplicate_query = Database::$db->prepare($get_duplicate);
                $get_duplicate_query->execute([':id' => $parent->getId(),
                    ':url_friendly' => $url_friendly]);
                $row_duplicate = $get_duplicate_query->fetch(\PDO::FETCH_ASSOC);
                
                // Check if any url patterns collide
                if (isset($row_duplicate['id'])) {
                    // Generate new url friendly
                    $url_friendly = Utilities::urlSafe($url_friendly_base . '-' . $num);
                    
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
            $name_base = $_POST['name'];
            $name = $_POST['name'];
            
            // Loop 'till no collides
            while (true) {
                $get_duplicate2  = "SELECT id" . PHP_EOL;
                $get_duplicate2 .= "FROM archive" . PHP_EOL;
                $get_duplicate2 .= "WHERE parent = :id" . PHP_EOL;
                $get_duplicate2 .= "AND name = :name";
                
                $get_duplicate2_query = Database::$db->prepare($get_duplicate2);
                $get_duplicate2_query->execute([':id' => $parent->getId(),
                    ':name' => $name]);
                $row_duplicate2 = $get_duplicate2_query->fetch(\PDO::FETCH_ASSOC);
                
                // Check if any url patterns collide
                if (isset($row_duplicate2['id'])) {
                    // Generate new url friendly
                    $name = $name_base . ' (' . $num . ')';
                    
                    // Increase num
                    $num++;
                }
                else {
                    // Gogog
                    break;
                }
            }
            
            // Set information
            $element = new Element();
            $element->setName($name);
            $element->setUrlFriendly($url_friendly);
            $element->setParent($parent->getId());
            $element->setDirectory(true);
            $element->setOwner(Me::getId());
            
            // Save element
            $element->save();
            
            // Check if parent was sat to empty and if we should update that
            if ($parent->isEmpty()) {
                $parent->setEmpty(false);
                $parent->update();
                
                // Clear cache on parent
                $parent->deleteCache();
            }
            
            // Add message
            MessageManager::addFileMessage($this->application, $_POST['name']);
            
            // Add history element
            $history = new History();
            $history->setUser(Me::getId());
            $history->setFile($element->getId());
            $history->setHistoryText('%u opprettet ' . $element->getName());
            $history->save();
            
            // Add karma
            $karma = new Karma();
            $karma->setUser(Me::getId());
            $karma->setFile($element->getId());
            $karma->setValue(2);
            $karma->save();
            
            // Add karma to user
            Me::increaseKarmaPending(2);
            Me::update();
            
            // Send successful code
            $this->setData('code', 200);
        }
        else {
            // Check what kind of error we encountered
            if (isset($_POST['name']) and strlen($_POST['name']) < 4) {
                $this->setData('code', 400);
            }
            else {
                $this->setData('code', 500);
            }
        }
    }
}

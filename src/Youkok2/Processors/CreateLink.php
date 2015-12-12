<?php
/*
 * File: CreateLink.php
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

class CreateLink extends BaseProcessor {
    
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
     * Constructor
     */

    public function __construct($method, $settings) {
        // Calling Base' constructor
        parent::__construct($method, $settings);
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
                // Check if any files was sent
                if (isset($_POST['url']) and filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
                    // This url is a valid url (according to php)
                    $request_ok = true;
                }
            }
        }
        
        // Check if we are good to go            
        if ($request_ok) {
            // For checking if we can post
            $can_post = true;
            
            // Trim away
            $_POST['url'] = rtrim(trim($_POST['url']));
            
            // Check if we should use name instead or url as name
            if (isset($_POST['name'])) {
                // Validate the name
                if (strlen($_POST['name']) >= 4) {
                    $name = $_POST['name'];
                }
                else {
                    $can_post = false;
                }
            }
            else {
                $name = $_POST['url'];
            }
            
            // Check if we can post
            if ($can_post) {
                // Check if duplicates exists
                $get_duplicate  = "SELECT id" . PHP_EOL;
                $get_duplicate .= "FROM archive " . PHP_EOL;
                $get_duplicate .= "WHERE parent = :id" . PHP_EOL;
                $get_duplicate .= "AND url = :url";
                
                $get_duplicate_query = Database::$db->prepare($get_duplicate);
                $get_duplicate_query->execute(array(':id' => $parent->getId(),
                    ':url' => $_POST['url']));
                $row_duplicate = $get_duplicate_query->fetch(\PDO::FETCH_ASSOC);
                
                // Check if any url patterns collide
                if (!isset($row_duplicate['id'])) {
                    // Set information
                    $element = new Element();
                    $element->setName($name);
                    $element->setUrlFriendly('');
                    $element->setParent($parent->getId());
                    $element->setUrl($_POST['url']);
                    
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
                    
                    // Check if parent was sat to empty and if we should update that
                    if (Me::isLoggedIn() and $parent->isEmpty()) {
                        $parent->setEmpty(false);
                        $parent->update();
                        
                        // Clear cache on parent
                        $parent->deleteCache();
                    }
                    
                    // Add message
                    MessageManager::addFileMessage($name);
                    
                    // Check if logged in
                    if (Me::isLoggedIn()) {
                        // Add history element
                        $history = new History();
                        $history->setUser(Me::getId());
                        $history->setFile($element->getId());
                        $history->setHistoryText('%u postet ' . $element->getName());
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
                    // Duplicate!
                    $this->setData('code', 400);
                }
            }
            else {
                // Name is too short
                $this->setData('code', 401);
            }
        }
        else {
            $this->setData('code', 500);
        }
    }
}
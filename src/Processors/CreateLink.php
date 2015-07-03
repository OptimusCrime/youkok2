<?php
/*
 * File: CreateLink.php
 * Holds: Create a new link
 * Created: 25.02.15
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

class CreateLink extends BaseProcessor {

    /*
     * Constructor
     */

    public function __construct($outputData = false, $returnData = false) {
        // Calling Base' constructor
        parent::__construct($outputData, $returnData);
        
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
        
        // Handle output
        if ($this->outputData) {
            $this->outputData();
        }
        if ($this->returnData) {
            return $this->returnData();
        }
    }
    
    /*
     * Process link
     */
    
    private function process() {
        $request_ok = false;
        
        // Check parent
        if (isset($_POST['id']) and is_numeric($_POST['id'])) {
            $parent = ElementCollection::get($_POST['id']);

            // Check if valid Element
            if ($parent->controller->wasFound()) {
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
                        $parent->controller->deleteCache();
                    }
                    
                    // Add message
                    MessageManager::addFileMessage($name);
                    
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
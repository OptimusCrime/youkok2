<?php
/*
 * File: CreateFolder.php
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

class CreateFolder extends Base {

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
            if (Me::isLoggedIn()) {
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
        $this->outputData();
    }
    
    /*
     * Process upload
     */
    
    private function process() {
        $request_ok = false;
        
        // Check parent
        if (isset($_POST['id']) and is_numeric($_POST['id'])) {
            $parent = ElementCollection::get($_POST['id']);

            // Check if valid Element
            if ($parent !== null) {
                // Check if name was sent
                if (isset($_POST['name']) and strlen($_POST['name']) >= 4) {
                    $request_ok = true;
                }
            }
        }
        
        // Check if we are good to go            
        if ($request_ok) {
            // Check duplicates for url friendly
            $letters = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
            $url_friendly = Utilities::urlSafe($_POST['name'], true);
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
            
            // Set information
            $element = new Element();
            $element->setName($_POST['name']);
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
                $parent->controller->deleteCache();
            }
            
            // Add message
            MessageManager::addFileMessage($_POST['name']);
            
            // Add history element
            $history = new History();
            $history->setUser(Me::getId());
            $history->setFile($element->getId());
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
                $this->setData('code', 401);
            }
            else {
                $this->setData('code', 500);
            }
        }
    }
}
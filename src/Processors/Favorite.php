<?php
/*
 * File: Register.php
 * Holds: Handlers for registering for a user
 * Created: 23.12.14
 * Project: Youkok2
 * 
*/

namespace Youkok2\Processors;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\Database as Database;

/*
 * The Static class, extending Base class
 */

class Favorite extends BaseProcessor {

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
                $this->updateFavorite();
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
     * Set or remove favorite for a element
     */

    private function updateFavorite() {
        if (isset($_POST['id']) and is_numeric($_POST['id']) and isset($_POST['type']) and 
            ($_POST['type'] == 'add' or $_POST['type'] == 'remove')) {
            
            // Check action
            if ($_POST['type'] == 'remove') {
                // Remove favorite
                $remove_favorite  = "DELETE FROM favorite" . PHP_EOL;
                $remove_favorite .= "WHERE file = :file AND user = :user";
                
                $remove_favorite_query = Database::$db->prepare($remove_favorite);
                $remove_favorite_query->execute(array(':file' => $_POST['id'], ':user' => Me::getId()));
                
                // Set message
                $this->setData('msg', [['type' => 'success', 'text' => 'Favoritten er fjernet.']]);
            }
            else {
                // Add favorte
                $insert_favorite  = "INSERT INTO favorite (file, user)" . PHP_EOL;
                $insert_favorite .= "VALUES (:file, :user)";
                
                $insert_favorite_query = Database::$db->prepare($insert_favorite);
                $insert_favorite_query->execute(array(':file' => $_POST['id'], ':user' => Me::getId()));
                
                // Set message
                $this->setData('msg', [['type' => 'success', 'text' => 'Lagt til som favoritt.']]);
            }
            
            // Set message
            $this->setData('code', 200);
        }
        else {
            $this->setData('code', 500);
            $this->setData('msg', 'Mangler enter id eller type');
        }
    }
}
<?php
/*
 * File: HistoryController.php
 * Holds: Interface for the controllers
 * Created: 24.02.15
 * Project: Youkok2
*/

namespace Youkok2\Models\Controllers;

/*
 * Define what classes to use
 */

use \Youkok2\Utilities\Database as Database;

/*
 * HistoryController extending BaseController
 */

class HistoryController implements BaseController {

    /*
     * Variables
     */

    private $model;

    /*
     * Constructor
     */

    public function __construct($model) {
        // Set pointer to the model
        $this->model = $model;
    }
    
    /*
     * Save method
     */
    
    public function save() {
        $insert_history  = "INSERT INTO history (user, file, history_text, added) " . PHP_EOL;
        $insert_history .= "VALUES (:user, :file, :text, NOW())";

        $insert_history_query = Database::$db->prepare($insert_history);
        $insert_history_query->execute([':user' => $this->model->getUser(),
            ':file' => $this->model->getFile(),
            ':text' => $this->model->getHistoryText()]);
        
        // Set id to model
        $this->model->setId(Database::$db->lastInsertId());
    }
    
    /*
     * Not implemented
     */
    
    public function cache() {}
    public function update() {}
    public function delete() {}
}
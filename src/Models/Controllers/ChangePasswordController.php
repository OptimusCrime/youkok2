<?php
/*
 * File: ChangePasswordController.php
 * Holds: Controller for the model ChangePassword
 * Created: 30.122014
 * Project: Youkok2
*/

namespace Youkok2\Models\Controllers;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\Database as Database;

/*
 * The class CourseController
 */

class ChangePasswordController implements BaseController {

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
     * Create by hash
     */

    public function createByHash($hash) {
        $validate_hash  = "SELECT c.id, c.user, c.timeout, u.email" . PHP_EOL;
        $validate_hash .= "FROM changepassword c" . PHP_EOL;
        $validate_hash .= "LEFT JOIN user AS u ON c.user = u.id" . PHP_EOL;
        $validate_hash .= "WHERE c.hash = :hash" . PHP_EOL;
        $validate_hash .= "AND c.timeout > NOW()";

        $validate_hash_query = Database::$db->prepare($validate_hash);
        $validate_hash_query->execute(array(':hash' => $hash));
        $row = $validate_hash_query->fetch(\PDO::FETCH_ASSOC);

        if (isset($row['id'])) {
            $this->model->setId($row['id']);
            $this->model->setUser($row['user']);
            $this->model->setHash($hash);
            $this->model->setTimeout($row['timeout']);

            // Set user email
            Me::setEmail($row['email']);
        }
    }

    /*
     * Save method (for a new ChangePassword)
     */

    public function save() {
        $insert_changepassword  = "INSERT INTO changepassword (user, hash, timeout) " . PHP_EOL;
        $insert_changepassword .= "VALUES (:user, :hash, NOW() + INTERVAL 1 DAY)";

        $insert_changepassword_query = Database::$db->prepare($insert_changepassword);
        $insert_changepassword_query->execute([':user' => $this->model->getUser(),
            ':hash' => $this->model->getHash()]);

        // Get the course-id
        $changepassword_id = Database::$db->lastInsertId();

        // Set id to model
        $this->model->setId($changepassword_id);
    }

    /*
     * Delete
     */

    public function delete() {
        $delete_changepassword  = "DELETE FROM changepassword" . PHP_EOL;
        $delete_changepassword .= "WHERE user = :user";

        $delete_changepassword_query = Database::$db->prepare($delete_changepassword);
        $delete_changepassword_query->execute(array(':user' => $this->model->getUser()));
    }

    /*
     * Not implemented
     */

    public function cache() {
    }
    public function update() {
    }
}
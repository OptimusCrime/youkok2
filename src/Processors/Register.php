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
use \Youkok2\Utilities\Loader as Loader;

/*
 * The Static class, extending Base class
 */

class Register extends Base
{

    /*
     * Constructor
     */

    public function __construct($returnData = false, $method = null, $data = []) {
        // Calling Base' constructor
        parent::__construct($returnData);

        // Try to connect to the database
        if ($this->makeDatabaseConnection()) {
            // Check if a method was provided
            if ($method !== null and method_exists($this, $method)) {
                // Call method directory
                call_user_func_array(array($this, $method), $data);
            }
            else {
                // Get actual request
                $request = Loader::getQuery();

                // Find what request is made
                if ($request == 'processor/register/email') {
                    // Check if provided email already is in the system
                    $this->checkEmail();
                }
                else {
                    // Processor not found
                    $this->setData('msg', 'Processor not found');
                    $this->setData('code', 500);
                }
            }
        }

        // Return data
        $this->returnData();
    }

    /*
     * Method for checking if email is already in the system
     */

    private function checkEmail() {
        // Check if valid request
        if (isset($_POST['email'])) {

            $check_email  = "SELECT COUNT(id) as num" . PHP_EOL;
            $check_email .= "FROM user" . PHP_EOL;
            $check_email .= "WHERE email = :email";

            $check_email_query = Database::$db->prepare($check_email);
            $check_email_query->execute(array(':email' => $_POST['email']));
            $row = $check_email_query->fetch(\PDO::FETCH_ASSOC);

            // Check if flag was returned
            if ((!isset($_POST['ignore']) and $row['num'] > 0) or (isset($_POST['ignore']) and $row['num'] > 1)) {
                $this->setData('code', 500);
            }
            else {
                $this->setData('code', 200);
            }
        }
        else {
            $this->setData('code', 500);
        }
    }
}
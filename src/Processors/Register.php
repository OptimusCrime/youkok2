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

    public function __construct($returnData = false)
    {
        // Calling Base' constructor
        parent::__construct($returnData);

        // Try to connect to the database
        if ($this->makeDatabaseConnection()) {
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

        // Return data
        $this->returnData();
    }

    /*
     * Method for checking if email is already in the system
     */

    private function checkEmail() {
        // Check if valid request
        if (isset($_POST['email']) and (isset($_POST['ignore']) or !Me::isLoggedIn())) {
            $check_email  = "SELECT id" . PHP_EOL;
            $check_email .= "FROM user" . PHP_EOL;
            $check_email .= "WHERE email = :email";

            $check_email_query = Database::$db->prepare($check_email);
            $check_email_query->execute(array(':email' => $_POST['email']));
            $row = $check_email_query->fetch(\PDO::FETCH_ASSOC);

            // Check if flag was returned
            if (isset($row['id'])) {
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
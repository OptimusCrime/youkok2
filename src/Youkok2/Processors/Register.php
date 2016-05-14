<?php
/*
 * File: Register.php
 * Holds: Handlers for registering for a user
 * Created: 23.12.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2\Processors;

use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Loader;

class Register extends BaseProcessor {
    
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
     * Method for checking if email is already in the system
     */

    public function checkEmail() {
        // Check if valid request
        if (isset($_POST['email'])) {
            if (isset($_POST['ignore'])) {
                // Log the user in
                Me::init();
                
                // Make sure we actually logged in
                if (Me::isLoggedIn()) {
                    $check_email  = "SELECT COUNT(id) as num" . PHP_EOL;
                    $check_email .= "FROM user" . PHP_EOL;
                    $check_email .= "WHERE email = :email" . PHP_EOL;
                    $check_email .= "AND email != :email_old";

                    $check_email_query = Database::$db->prepare($check_email);
                    $check_email_query->execute(array(':email' => $_POST['email'],
                        ':email_old' => Me::getEmail()));
                    $row = $check_email_query->fetch(\PDO::FETCH_ASSOC);
                    
                    // Check if flag was returned
                    if ($row['num'] > 0) {
                        $this->setData('code', 500);
                    }
                    else {
                        $this->setData('code', 200);
                    }
                }
                else {
                    // Could not log in, can't check the email correctly
                    $this->setData('code', 500);
                }
            }
            else {
                $check_email  = "SELECT COUNT(id) as num" . PHP_EOL;
                $check_email .= "FROM user" . PHP_EOL;
                $check_email .= "WHERE email = :email";

                $check_email_query = Database::$db->prepare($check_email);
                $check_email_query->execute(array(':email' => $_POST['email']));
                $row = $check_email_query->fetch(\PDO::FETCH_ASSOC);

                // Check if flag was returned
                if ($row['num'] > 0) {
                    $this->setData('code', 500);
                }
                else {
                    $this->setData('code', 200);
                }
            }
        }
        else {
            $this->setData('code', 500);
        }
    }
}
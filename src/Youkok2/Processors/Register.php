<?php
namespace Youkok2\Processors;

use Youkok2\Models\Me;
use Youkok2\Utilities\Database;

class Register extends BaseProcessor
{

    protected function requireDatabase() {
        return true;
    }
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function checkEmail() {
        if (isset($_POST['email'])) {
            if (isset($_POST['ignore'])) {
                $this->me = new Me($this->application);
                
                if ($this->me->isLoggedIn()) {
                    $check_email  = "SELECT COUNT(id) as num" . PHP_EOL;
                    $check_email .= "FROM user" . PHP_EOL;
                    $check_email .= "WHERE email = :email" . PHP_EOL;
                    $check_email .= "AND email != :email_old";

                    $check_email_query = Database::$db->prepare($check_email);
                    $check_email_query->execute([':email' => $_POST['email'],
                        ':email_old' => $this->me->getEmail()]);
                    $row = $check_email_query->fetch(\PDO::FETCH_ASSOC);
                    
                    if ($row['num'] > 0) {
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
            else {
                $check_email  = "SELECT COUNT(id) as num" . PHP_EOL;
                $check_email .= "FROM user" . PHP_EOL;
                $check_email .= "WHERE email = :email";

                $check_email_query = Database::$db->prepare($check_email);
                $check_email_query->execute([':email' => $_POST['email']]);
                $row = $check_email_query->fetch(\PDO::FETCH_ASSOC);

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

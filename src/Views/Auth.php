<?php
/*
 * File: auth.controller.php
 * Holds: The AuthController-class
 * Created: 14.04.14
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Redirect as Redirect;
use \Youkok2\Utilities\MessageManager as MessageManager;
use \Youkok2\Utilities\Utilities as Utilities;

/*
 * The Flat class, extending Base class
 */


class Auth extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();

        // Reset menu
        $this->template->assign('HEADER_MENU', null);

        // Check query
        if ($this->queryGet(0) == 'logg-inn') {
            // Check if logged in
            if (Me::isLoggedIn()) {
                Redirect::send('');
            }
            else {
                // Check if submitted
                if (isset($_POST['login2-email']) or isset($_POST['login-email'])) {
                    // Change post vars
                    if (isset($_POST['login2-email'])) {
                        $_POST['login-email'] = $_POST['login2-email'];
                        $_POST['login-pw'] = $_POST['login2-pw'];
                    }

                    // Call method
                    Me::login();
                }
                else {
                    $this->template->assign('SITE_TITLE', 'Logg inn');

                    if (isset($_SESSION['login_correct_email'])) {
                        $this->template->assign('LOGIN_EMAIL', $_SESSION['login_correct_email']);
                        unset($_SESSION['login_correct_email']);
                    }

                    $this->displayAndCleanup('login.tpl');
                }
            }
        }
        else if ($this->queryGet(0) == 'logg-ut') {
            if (!Me::isLoggedIn()) {
                Redirect::send('');
            }
            else {
                Me::logOut();
            }
        }
        else if ($this->queryGet(0) == 'registrer') {
            if (Me::isLoggedIn()) {
                Redirect::send('');
            }
            else {
                $this->template->assign('SITE_TITLE', 'Registrer');
                $this->register();
            }
        }
        else if ($this->queryGet(0) == 'glemt-passord') {
            if (Me::isLoggedIn()) {
                Redirect::send('');
            }
            else {
                $this->template->assign('SITE_TITLE', 'Glemt passord');
                $this->forgottenPassword();
            }
        }
        else if ($this->queryGet(0) == 'nytt-passord') {
            if (Me::isLoggedIn() or !isset($_GET['hash'])) {
                Redirect::send('');
            }
            else {
                $this->template->assign('SITE_TITLE', 'Nytt passord');
                $this->forgottenPasswordNew();
            }
        }
        else {
            // Page not found!
            $this->display404();
        }
    }

    //
    // Method for registering a user
    //

    private function register() {
        if (!isset($_POST['register-form-email'])) {
            $this->displayAndCleanup('register.tpl');
        }
        else {
            // Check all fields
            $fields = array('register-form-email', 'register-form-nick', 'register-form-password1', 'register-form-password2');
            $err = false;
            foreach ($fields as $v) {
                if (!isset($_POST[$v]) or ($v != 'register-form-nick' and strlen($_POST[$v]) < 7)) {
                    $err = true;
                    break;
                }
            }

            // Check if missing data
            if (!$err) {
                // Set post variables
                $this->setFormValues('post', ['email' => $_POST['register-form-email']]);

                // Run processor
                $email_check = ($this->runProcessor('register/email', true, 'checkEmail')->returnData());

                // Check if valid email
                if (isset($email_check['code']) and $email_check['code'] == 200 and filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == true) {
                    // Check passwords
                    if ($_POST['register-form-password1'] == $_POST['register-form-password2']) {
                        // Match, create new password
                        $hash = Utilities::hashPassword($_POST['register-form-password1'], Utilities::generateSalt());

                        // Insert to database
                        Me::setEmail($_POST['register-form-email']);
                        Me::setPassword($hash);
                        Me::setNick($_POST['register-form-nick']);

                        // Create
                        Me::create();

                        // Send e-mail here
                        $mail = new \PHPMailer;
                        $mail->From = 'donotreply@' . DOMAIN;
                        $mail->FromName = 'Youkok2';
                        $mail->addAddress($_POST['register-form-email']);
                        $mail->addReplyTo(EMAIL_CONTACT);

                        $mail->WordWrap = 75;
                        $mail->isHTML(false);

                        $mail->Subject = utf8_decode('Velkommen til Youkok2');
                        $mail->Body = utf8_decode(file_get_contents(BASE_PATH . '/files/mail/register.txt'));
                        $mail->send();
                    }
                    else {
                        $err = true;
                    }
                }
                else {
                    $err = true;
                }
            }

            // Check if there was any errors during the signup
            if ($err) {
                // Add message
                MessageManager::addMessage('Her gikk visst noe galt...', 'danger');
               
                // Redirect
                Redirect::send('registrer');
            }
            else {
                // Add message
                MessageManager::addMessage('Velkommen til Youkok2!', 'success');

                // Log in (only session)
                Me::setLogin($hash, $_POST['register-form-email']);

                Redirect::send('');
            }
        }
    }

    //
    // Method for forgotten password
    //

    private function forgottenPassword() {
        if (isset($_POST['forgotten-email'])) {
            // Handle stuff here
            $get_login_user  = "SELECT id" . PHP_EOL;
            $get_login_user .= "FROM user" . PHP_EOL;
            $get_login_user .= "WHERE email = :email";

            $get_login_user_query = Database::$db->prepare($get_login_user);
            $get_login_user_query->execute(array(':email' => $_POST['forgotten-email']));
            $row = $get_login_user_query->fetch(\PDO::FETCH_ASSOC);

            // Check result
            if (isset($row['id'])) {
                // Create hash
                $hash = Utilities::hashPassword(md5(rand(0, 100000) . md5(time()) . $row['id']), sha1(rand(0, 1000)), false);

                // Create database entry
                $insert_changepassword  = "INSERT INTO changepassword" . PHP_EOL;
                $insert_changepassword .= "(user, hash, timeout)" . PHP_EOL;
                $insert_changepassword .= "VALUES (:user, :hash, NOW() + INTERVAL 1 DAY)";
                
                $insert_changepassword_query = Database::$db->prepare($insert_changepassword);
                $insert_changepassword_query->execute(array(':user' => $row['id'], ':hash' => $hash));

                // Send mail
                $mail = new \PHPMailer;
                $mail->From = 'donotreply@' . DOMAIN;
                $mail->FromName = 'Youkok2';
                $mail->addAddress($_POST['forgotten-email']);
                $mail->addReplyTo(EMAIL_CONTACT);

                $mail->WordWrap = 75;
                $mail->isHTML(false);

                $mail->Subject = utf8_decode('Glemt passord på Youkok2');
                $message = utf8_decode(file_get_contents(BASE_PATH . '/files//mail/forgotten.txt'));
                $message_keys = array(
                    '{{SITE_URL}}' => URL_FULL,
                    '{{HASH}}' => $hash);
                $mail->Body = str_replace(array_keys($message_keys), $message_keys, $message);
                $mail->send();

                // Add message
                MessageManager::addMessage('Det er blitt sendt en e-post til deg. Denne inneholder en link for å velge nytt passord. Denne linken er gyldig i 24 timer.', 'success');
            }
            else {
                MessageManager::addMessage('E-posten du oppga ble ikke funnet i systemet. Prøv igjen.', 'danger');
            }

            // Redirect back to form
            Redirect::send('glemt-passord');
        }
        else {
            $this->displayAndCleanup('forgotten_password.tpl');
        }
    }

    //
    // Method for changing password
    //

    private function forgottenPasswordNew() {
        // Check if changepassword was found
        $validate_hash  = "SELECT c.id, c.user, u.email" . PHP_EOL;
        $validate_hash .= "FROM changepassword c" . PHP_EOL;
        $validate_hash .= "LEFT JOIN user AS u ON c.user = u.id" . PHP_EOL;
        $validate_hash .= "WHERE c.hash = :hash" . PHP_EOL;
        $validate_hash .= "AND c.timeout > NOW()";
        
        $validate_hash_query = Database::$db->prepare($validate_hash);
        $validate_hash_query->execute(array(':hash' => $_GET['hash']));
        $row = $validate_hash_query->fetch(\PDO::FETCH_ASSOC);

        // Check if valid or not
        if (isset($row['id'])) {
            // Check if submitted
            if (!isset($_POST['forgotten-password-new-form-password1'])) {
                // Display
                $this->displayAndCleanup('forgotten_password_new.tpl');
            }
            else {
                // Check if the two passwords are identical
                if ($_POST['forgotten-password-new-form-password1'] == $_POST['forgotten-password-new-form-password2']) {

                    // New hash
                    $hash_salt = Utilities::generateSalt();
                    $hash = Utilities::hashPassword($_POST['forgotten-password-new-form-password1'], $hash_salt);

                    // Insert
                    $insert_user_new_password  = "UPDATE user" . PHP_EOL;
                    $insert_user_new_password .= "SET password = :password" . PHP_EOL;
                    $insert_user_new_password .= "WHERE id = :user";

                    $insert_user_new_password_query = Database::$db->prepare($insert_user_new_password);
                    $insert_user_new_password_query->execute(array(':password' => $hash, ':user' => $row['user']));

                    // Delete from changepassword
                    $delete_changepassword  = "DELETE FROM changepassword" . PHP_EOL;
                    $delete_changepassword .= "WHERE user = :user";

                    $delete_changepassword_query = Database::$db->prepare($delete_changepassword);
                    $delete_changepassword_query->execute(array(':user' => $row['user']));

                    // Add message
                    MessageManager::addMessage('Passordet er endret!', 'success');

                    // Log in (only session)
                    Me::setLogin($hash, $row['email']);

                    Redirect::send('');
                }
                else {
                    // Add error message
                    MessageManager::addMessage('De to passordene er ikke like.', 'danger');

                    // Redirect
                    Redirect::send('nytt-passord?hash=' . $_GET['hash']);
                }
            }
        }
        else {
            // Add error message
            MessageManager::addMessage('Denne linken er ikke lenger gyldig.', 'danger');

            // Redirect
            Redirect::send('');
        }
    }
}
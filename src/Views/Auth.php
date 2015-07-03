<?php
/*
 * File: Auth.php
 * Holds: The Auth-class
 * Created: 14.04.2014
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
 * The Flat class, extending BaseView
 */


class Auth extends BaseView {

    /*
     * Constructor
     */

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();

        // Reset menu
        $this->template->assign('HEADER_MENU', null);
    }
    
    /*
     * Log in
     */
    
    public function displayLogIn() {
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
    
    /*
     * Log out
     */
    
    public function displayLogOut() {
        // Check if the user is logged in
        if (!Me::isLoggedIn()) {
            Redirect::send('');
            exit();
        }
        
        // Log the user out
        Me::logOut();
    }

    /*
     * Register
     */

    public function displayRegister() {
        // Check if the user is logged in
        if (Me::isLoggedIn()) {
            Redirect::send('');
            exit();
        }
        
        // Set view
        $this->addSiteData('view', 'register');
        
        // Set menu
        $this->template->assign('SITE_TITLE', 'Registrer');
        
        // Handle registration
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
                $email_check = $this->runProcessor('register/email', false, true);
                
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

                        // Save
                        Me::save();

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

    /*
     * Forgotten password
     */

    public function displayForgottenPassword() {
        if (Me::isLoggedIn()) {
            Redirect::send('');
            exit();
        }
        
        // The menu
        $this->template->assign('SITE_TITLE', 'Glemt passord');
        
        // Handle forgotten password
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

                // Set data
                $change_password = new ChangePassword();
                $change_password->setUser($row['id']);
                $change_password->setHash($hash);

                // Save
                $change_password->save();

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

    /*
     * Forgotten new password
     */

    public function displayForgottenPasswordNew() {
        // Check if user can display this view
        if (Me::isLoggedIn() or !isset($_GET['hash'])) {
            Redirect::send('');
            exit();
        }
        
        // Set view
        $this->addSiteData('view', 'forgotten-password');
        
        // Set menu
        $this->template->assign('SITE_TITLE', 'Nytt passord');
        
        // Check if changepassword was found
        $change_password = new ChangePassword();
        $change_password->createByHash($_GET['hash']);

        // Check if valid or not
        if ($change_password->getId() != null) {
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
                    Me::setPassword($hash);
                    Me::update();

                    // Delete from changepassword
                    $change_password->delete();

                    // Add message
                    MessageManager::addMessage('Passordet er endret!', 'success');

                    // Log in (only session)
                    Me::setLogin($hash, Me::getEmail());

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
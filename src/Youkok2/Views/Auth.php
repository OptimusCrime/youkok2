<?php
/*
 * File: Auth.php
 * Holds: Various authentification related views
 * Created: 14.04.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views;

use Youkok2\Models\Me;
use Youkok2\Models\User;
use Youkok2\Models\ChangePassword;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\MessageManager;
use Youkok2\Utilities\TemplateHelper;
use Youkok2\Utilities\Utilities;

class Auth extends BaseView
{
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);

        // Reset menu
        $this->template->assign('HEADER_MENU', null);
    }
    
    /*
     * Log in
     */
    
    public function displayLogIn() {
        // Check if logged in
        if (Me::isLoggedIn()) {
            $this->application->send('');
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
                Me::login($this->application);
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
            $this->application->send('');
        }
        
        // Log the user out
        Me::logOut($this->application);
    }

    /*
     * Register
     */

    public function displayRegister() {
        // Check if the user is logged in
        if (Me::isLoggedIn()) {
            $this->application->send('');
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
            $fields = [
                'register-form-email',
                'register-form-nick',
                'register-form-password1',
                'register-form-password2'
            ];

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
                $_POST['email'] = $_POST['register-form-email'];

                // Run processor
                $email_check = $this->runProcessor('register/email', false, true);
                
                // Check if valid email
                if (isset($email_check['code']) and $email_check['code'] == 200 and
                    filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == true) {
                    // Check passwords
                    if ($_POST['register-form-password1'] == $_POST['register-form-password2']) {
                        // Match, create new password
                        $hash = Utilities::hashPassword($_POST['register-form-password1'], Utilities::generateSalt());
                        
                        // New instace of me
                        Me::create();
                        
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
                MessageManager::addMessage($this->application, 'Her gikk visst noe galt...', 'danger');
               
                // Redirect
                $this->application->send(TemplateHelper::urlFor('auth_register'));
            }
            else {
                // Add message
                MessageManager::addMessage($this->application, 'Velkommen til Youkok2!', 'success');

                // Log in (only session)
                Me::setLogin($this->application, $hash, $_POST['register-form-email']);

                $this->application->send('');
            }
        }
    }

    /*
     * Forgotten password
     */

    public function displayForgottenPassword() {
        if (Me::isLoggedIn()) {
            $this->application->send('');
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
            $get_login_user_query->execute([':email' => $_POST['forgotten-email']]);
            $row = $get_login_user_query->fetch(\PDO::FETCH_ASSOC);

            // Check result
            if (isset($row['id'])) {
                // Create hash
                $hash = Utilities::hashPassword(
                    md5(rand(0, 100000) . md5(time()) . $row['id']),
                    sha1(rand(0, 1000)),
                    false
                );

                // Set data
                $change_password = new ChangePassword();
                $change_password->setUser($row['id']);
                $change_password->setHash($hash);
                $change_password->setTimeout(date('Y-m-d H:i:s', (time() + (60 * 60 * 24))));

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
                $message_keys = [
                    '{{SITE_URL}}' => URL_FULL,
                    '{{PATH}}' => TemplateHelper::urlFor('auth_new_password'),
                    '{{HASH}}' => $hash
                ];
                $mail->Body = str_replace(array_keys($message_keys), $message_keys, $message);
                $mail->send();

                // Add message
                $message  = 'Det er blitt sendt en e-post til deg. Denne inneholder en link for ';
                $message .= 'å velge nytt passord. Denne linken er gyldig i 24 timer.';
                MessageManager::addMessage($this->application, $message, 'success');
            }
            else {
                MessageManager::addMessage(
                    $this->application,
                    'E-posten du oppga ble ikke funnet i systemet. Prøv igjen.',
                    'danger'
                );
            }

            // Redirect back to form
            $this->application->send(TemplateHelper::urlFor('auth_forgotten_password'));
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
            $this->application->send('');
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
                if ($_POST['forgotten-password-new-form-password1'] ==
                    $_POST['forgotten-password-new-form-password2']) {
                    // Get the correct user object
                    $user = new User($change_password->getUser());
                    
                    // New hash
                    $hash_salt = Utilities::generateSalt();
                    $hash = Utilities::hashPassword($_POST['forgotten-password-new-form-password1'], $hash_salt);

                    // Insert
                    $user->setPassword($hash);
                    $user->update();

                    // Delete from changepassword
                    $change_password->delete();

                    // Add message
                    MessageManager::addMessage($this->application, 'Passordet er endret!', 'success');

                    // Log in (only session)
                    Me::setLogin($this->application, $hash, $user->getEmail());

                    $this->application->send('');
                }
                else {
                    // Add error message
                    MessageManager::addMessage($this->application, 'De to passordene er ikke like.', 'danger');

                    // Redirect
                    $this->application->send(TemplateHelper::urlFor('auth_new_password') . '?hash=' . $_GET['hash']);
                }
            }
        }
        else {
            // Add error message
            MessageManager::addMessage($this->application, 'Denne linken er ikke lenger gyldig.', 'danger');

            // Redirect
            $this->application->send('');
        }
    }
}

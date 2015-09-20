<?php
/*
 * File: Profile.php
 * Holds: Views for the profile
 * Created: 02.10.2013
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\Loader as Loader;
use \Youkok2\Utilities\MessageManager as MessageManager;
use \Youkok2\Utilities\Redirect as Redirect;
use \Youkok2\Utilities\Utilities as Utilities;

class Profile extends BaseView {

    /*
     * Constructor
     */

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();
        
        // Set menu
        $this->template->assign('HEADER_MENU', '');

        // Make sure user us logged in
        if (!Me::isLoggedIn()) {
            Redirect::send('');
        }
        else {
            // Match subquery
            if (Loader::queryGet(1) == 'innstillinger') {
                // Display settings
                $this->profileSettings();
            }
            else if (Loader::queryGet(1) == 'historikk') {
                // Display history
                $this->profileHistory();
            }
            else {
                // 404, not found
                $this->display404();
            }
        }
    }
    
    /*
     * Display profile settings
     */
    
    private function profileSettings() {
         if (!isset($_POST['source'])) {
            // Set title
            $this->template->assign('SITE_TITLE', 'Mine innstillinger');

            // For info
            $this->template->assign('USER_EMAIL', Me::getEmail());

            // Display
            $this->displayAndCleanup('profile_settings.tpl');
        }
        else {
            // Not submitted, display views
            if ($_POST['source'] == 'password') {
                $this->profileUpdatePassword();
            }
            else if ($_POST['source'] == 'info') {
                $this->profileUpdateInfo();
            }
            else {
                Redirect::send('');
            }
        }
    }

    /*
     * Display profile history
     */

    private function profileHistory() {
        // Set title
        $this->template->assign('SITE_TITLE', 'Mine historikk');
        
        // Get the elements for this history table
        $history = Me::getKarmaElements();
        $this->template->assign('PROFILE_USER_HISTORY', $history);
        
        // Display
        $this->displayAndCleanup('profile_history.tpl');
    }
    
    /*
     * Update password
     */

    private function profileUpdatePassword() {
        // Check if everything is ok
        if (isset($_POST['forgotten-password-new-form-oldpassword'])
            and isset($_POST['forgotten-password-new-form-password1'])
            and isset($_POST['forgotten-password-new-form-password2'])
            and ($_POST['forgotten-password-new-form-password1'] == $_POST['forgotten-password-new-form-password2'])) {

            // Validate old password
            if (password_verify($_POST['forgotten-password-new-form-oldpassword'], Utilities::reverseFuckup(Me::getPassword()))) {
                // New hash
                $hash_salt = Utilities::generateSalt();
                $hash = Utilities::hashPassword($_POST['forgotten-password-new-form-password1'], $hash_salt);

                // Set data
                Me::setPassword($hash);

                // Update
                Me::update();

                // Add message
                MessageManager::addMessage('Passordet er endret.', 'success');

                // Check if we should set more than just session
                $set_login_cookie = false;
                if (isset($_COOKIE['youkok2'])) {
                    $set_login_cookie = true;
                }

                // Set the login
                Me::setLogin($hash, Me::getEmail(), $set_login_cookie);

                // Do the redirect
                Redirect::send('profil/innstillinger');
            }
            else {
                // Add message
                MessageManager::addMessage('Passordet du oppga som ditt gamle passord er ikke korrekt eller de nye passordene matchet ikke. Prøv igjen.', 'danger');

                // Redirect
                Redirect::send('profil/innstillinger');
            }
        }
    }

    /*
     * Update profile info
     */

    private function profileUpdateInfo() {
        if (isset($_POST['register-form-email']) and isset($_POST['register-form-nick'])) {
            // Make sure there is no other users with this email
            $email_processor = null;
            if ($_POST['register-form-email'] != Me::getEmail()) {
                $_POST['email'] = $_POST['register-form-email'];
                $_POST['ignore'] = 1;
                
                $email_processor = self::runProcessor('/register/email',[
                    'output' => false,
                    'close_db' => false]);
            }
            
            // Check if we should update e-mail
            if (($email_processor != null and $email_processor['code'] == 200)
                and $_POST['register-form-email'] != Me::getEmail()
                and strlen($_POST['register-form-email']) > 0
                and filter_var($_POST['register-form-email'], FILTER_VALIDATE_EMAIL)) {
                
                
                
                // Set data
                Me::setEmail($_POST['register-form-email']);

                // Update
                Me::update();

                // Update cookie/session
                $set_login_cookie = false;
                if (isset($_COOKIE['youkok2'])) {
                    $set_login_cookie = true;
                }

                // Try to split
                $hash_split = explode('asdkashdsajheeeeehehdffhaaaewwaddaaawww', $_SESSION['youkok2']);

                // Set the login
                Me::setLogin($hash_split[1], $_POST['register-form-email'], $set_login_cookie);
            }

            // Check if we should update nick
            if (($_POST['register-form-nick'] == '' and Me::getNick() != '<em>Anonym</em>') or ($_POST['register-form-nick'] != Me::getNickReal())) {
                Me::setNick($_POST['register-form-nick']);
            }

            // Save here
            Me::update();

            // Add messag
            MessageManager::addMessage('Dine endringer er lagret.', 'success');

            // Redirect
            Redirect::send('profil/innstillinger');
        }
        else {
            // Add messag
            MessageManager::addMessage('Her gikk visst noe galt...', 'danger');

            // Redirect
            Redirect::send('profil/innstillinger');
        }
    }
}
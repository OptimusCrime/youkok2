<?php
/*
 * File: Profile.php
 * Holds: Views for the profile
 * Created: 02.10.13
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\MessageManager as MessageManager;
use \Youkok2\Utilities\Redirect as Redirect;
use \Youkok2\Utilities\Utilities as Utilities;

/*
 * The Profile class, extending Base class
 */

class Profile extends Base {

    /*
     * Constructor
     */

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();

        // Check if online
        if (Me::isLoggedIn()) {
            if ($this->queryGetClean() == 'profil/innstillinger') {
                if (!isset($_POST['source'])) {
                    // Set status
                    Me::setUserStatus($this, 'PROFILE');

                    // For info
                    $this->template->assign('PROFILE_USER_EMAIL', Me::getEmail());
                    $this->template->assign('PROFILE_USER_EMAIL', Me::getEmail());
                    $this->template->assign('PROFILE_USER_NICK', Me::getNick());

                    // Displaying and cleaning up
                    $this->template->assign('SITE_TITLE', 'Mine innstillinger');
                    $this->displayAndCleanup('profile_settings.tpl');
                }
                else {
                    if ($_POST['source'] == 'password') {
                        $this->profileUpdatePassword();
                    }
                    else if ($_POST['source'] == 'info') {
                        $this->profileUpdateInfo();
                    }
                    else {
                        $this->redirect('');
                    }
                }

            }
            else if ($this->queryGetClean() == 'profil/historikk') {
                $this->profileHistory();

                // Displaying and cleaning up
                $this->template->assign('SITE_TITLE', 'Mine historikk');
                $this->displayAndCleanup('profile_history.tpl');
            }
            else {
                // Not found
                $this->display404();
            }
        }
        else {
            $this->redirect('');
        }
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
            if (password_verify($_POST['forgotten-password-new-form-oldpassword'], Utility::reverseFuckup(Me::getPassword()))) {
                // New hash
                $hash_salt = Utilities::generateSalt();
                $hash = Utilities::hashPassword($_POST['forgotten-password-new-form-password1'], $hash_salt);

                // Insert
                $insert_user_new_password  = "UPDATE user" . PHP_EOL;
                $insert_user_new_password .= "SET password = :password" . PHP_EOL;
                $insert_user_new_password .= "WHERE id = :user";

                $insert_user_new_password_query = Database::$db->prepare($insert_user_new_password);
                $insert_user_new_password_query->execute(array(':password' => $hash, ':user' => Me::getId()));

                // Add message
                $this->addMessage('Passordet er endret!', 'success');

                // Check if we should set more than just session
                if (isset($_COOKIE['youkok2'])) {
                    $set_login_cookie = true;
                }
                else {
                    $set_login_cookie = false;
                }

                // Set the login
                $this->user->setLogin($hash, $this->user->getEmail(), $set_login_cookie);

                // Do the redirect
                $this->redirect('profil/innstillinger');
            }
            else {
                // Add message
                $this->addMessage('Passordet du oppga som ditt gamle passord er ikke korrekt. Prøv igjen!', 'danger');

                // Redirect
                $this->redirect('profil/innstillinger');
            }
        }
    }

    //
    // Update profile info
    //

    private function profileUpdateInfo() {
        if (isset($_POST['register-form-email']) and isset($_POST['register-form-nick'])) {
            $error = false;

            // Check if we should update e-mail
            if ($_POST['register-form-email'] != Me::getEmail()
                and strlen($_POST['register-form-email']) > 0
                and filter_var($_POST['register-form-email'], FILTER_VALIDATE_EMAIL)) {

                // Set data
                Me::setEmail($_POST['register-form-email']);

                /* TODO
                 * // Store new email
                    $update_user_email = "UPDATE user
                    SET email = :email
                    WHERE id = :user";

                    $update_user_email_query = $this->db->prepare($update_user_email);
                    $update_user_email_query->execute(array(':email' => $_POST['register-form-email'],
                                                            ':user' => $this->user->getId()));

                    // Update cookie/session
                    if (isset($_COOKIE['youkok2'])) {
                        $hash = $_COOKIE['youkok2'];
                        $set_login_cookie = true;
                    }
                    else {
                        $hash = $_SESSION['youkok2'];
                        $set_login_cookie = false;
                    }

                    // Try to split
                    $hash_split = explode('asdkashdsajheeeeehehdffhaaaewwaddaaawww', $hash);
                    if (count($hash_split) == 2) {
                        // Set the login
                        $this->user->setLogin($hash_split[1], $_POST['register-form-email'], $set_login_cookie);
                    }
                    else {
                        $error = true;
                    }
                 */
            }

            // Check if we should update nick
            if ($_POST['register-form-nick'] != Me::getNickReal()) {
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

    //
    // Display user history
    //

    public function profileHistory() {
        $ret = '';

        $get_profile_history = "SELECT *
        FROM history
        WHERE user = :user
        ORDER BY added DESC";
        
        $get_profile_history_query = $this->db->prepare($get_profile_history);
        $get_profile_history_query->execute(array(':user' => $this->user->getId()));
        while ($row = $get_profile_history_query->fetch(PDO::FETCH_ASSOC)) {
            // Build element
            $element = new Item($this);
            $element->setLoadIfRemoved(true);
            $element->createById($row['file']);
            $this->collection->add($element);

            // Check if element was found
            if ($element->wasFound()) {
                // Check if we should fetch parent to build logical link
                if ($element->getParent() != 1) {
                    $parent_element = new Item($this);
                    $parent_element->createById($element->getParent());
                    $this->collection->add($parent_element);

                    $element_url = $parent_element->generateUrl($this->routes['archive'][0]);
                }
                else {
                    $element_url = $element->generateUrl($this->routes['archive'][0]);
                }

                $element_section = '<a href="' . $element_url . '">' . $element->getName() . '</a>';
            }
            else {
                // Was not found
                $element_section = 'Ukjent fil';
            }

            // Get type
            $type = History::$historyType[$row['type']];

            // Set classes according to values
            $list_class = '';
            if ($row['positive'] == 0) {
                $list_class = ' list-group-item-danger';
                $karma_prefix = '-';
            }
            else {
                if ($row['active'] == 0) {
                    $list_class = ' list-group-item-success';
                }
                $karma_prefix = '+';
            }

            // Build inner string
            $inner = '<div class="width33">' . $element_section . '</div><div class="width33">' . $type . '</div><div class="width33"><span class="moment-timestamp" style="cursor: help;" title="' . $this->utils->prettifySQLDate($row['added']) . '" data-ts="' . $row['added'] . '">Laster...</span><span class="badge">' . $karma_prefix . $row['karma'] . '</span></div>';
            
            // Build outer string    
            $ret .= '<li class="list-group-item' . $list_class . '">' . $inner . '</li>';
        }

        if ($ret == '') {
            $ret = '<li class="list-group-item">Du har ikke opparbeidet deg noe karma!</li>';
        }

        $this->template->assign('PROFILE_USER_HISTORY', $ret);
    }
}
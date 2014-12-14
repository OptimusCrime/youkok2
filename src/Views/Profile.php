<?php
/*
 * File: profile.controller.php
 * Holds: The ProfileController-class
 * Created: 02.10.13
 * Project: Youkok2
 * 
*/

//
// The ProfileController. Handles different profile stuff
//

class ProfileController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($routes) {
        // Calling Base' constructor
        parent::__construct($routes);

        // Check if online
        if ($this->user->isLoggedIn()) {
            if ($this->queryGetClean() == 'profil/innstillinger') {
                if (!isset($_POST['source'])) {
                    // Assign email
                    $this->template->assign('PROFILE_USER_EMAIL', $this->user->getEmail());

                    if ($this->user->isBanned()) {
                        $this->template->assign('PROFILE_USER_ACTIVE', 0);
                    }
                    else {
                        $this->template->assign('PROFILE_USER_ACTIVE', 1);
                    }

                    if ($this->user->canContribute()) {
                        $this->template->assign('PROFILE_USER_CAN_CONTRIBUTE', 1);
                    }
                    else {
                        $this->template->assign('PROFILE_USER_CAN_CONTRIBUTE', 0);
                    }

                    // For info
                    $this->template->assign('PROFILE_USER_EMAIL', $this->user->getEmail());
                    $this->template->assign('PROFILE_USER_NICK', $this->user->getNick());

                    // Displaying and cleaning up
                    $this->template->assign('SITE_TITLE', 'Mine innstillinger');
                    $this->displayAndCleanup('profile_settings.tpl');
                }
                else {
                    if ($_POST['source'] == 'password') {
                        $this->profilePassword();
                    }
                    else if ($_POST['source'] == 'info') {
                        $this->profileInfo();
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

    //
    // Method for updating password
    //

    private function profilePassword() {
        if ($this->user->isLoggedIn() and isset($_POST['forgotten-password-new-form-oldpassword'])
            and isset($_POST['forgotten-password-new-form-password1'])
            and isset($_POST['forgotten-password-new-form-password2'])) {

            if ($_POST['forgotten-password-new-form-password1'] == $_POST['forgotten-password-new-form-password2']) {
                // Get salt
                $get_user_salt = "SELECT salt, password
                FROM user
                WHERE id = :user";

                $get_user_salt_query = $this->db->prepare($get_user_salt);
                $get_user_salt_query->execute(array(':user' => $this->user->getId()));
                $row2 = $get_user_salt_query->fetch(PDO::FETCH_ASSOC);

                // Check if user was found
                if (isset($row2['salt'])) {
                    // Generate old hash
                    $hash_pre = $this->user->hashPassword($_POST['forgotten-password-new-form-oldpassword'], $row2['salt']);

                    // Check if the old password matches the old one
                    if ($hash_pre == $row2['password']) {
                        // Generate new hash
                        $hash = $this->user->hashPassword($_POST['forgotten-password-new-form-password1'], $row2['salt']);

                        // Insert
                        $insert_user_new_password = "UPDATE user
                        SET password = :password
                        WHERE id = :user";

                        $insert_user_new_password_query = $this->db->prepare($insert_user_new_password);
                        $insert_user_new_password_query->execute(array(':password' => $hash,
                                                                       ':user' => $this->user->getId()));

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
                else {
                    // Add error message
                    $this->addMessage('Her gikk visst noe galt...', 'danger');

                    // Redirect
                    $this->redirect('profil/innstillinger');
                }
            }
        }
        else {
            $this->addMessage('Her gikk visst noe galt...', 'danger');

            // Redirect
            $this->redirect('profil/innstillinger');
        }
    }

    //
    // Update profile info
    //

    private function profileInfo() {
        if ($this->user->isLoggedIn() and isset($_POST['register-form-email']) and isset($_POST['register-form-nick'])) {
            $error = false;

            // Check if we should update e-mail
            if ($_POST['register-form-email'] != $this->user->getEmail()
                and strlen($_POST['register-form-email']) > 0
                and filter_var($_POST['register-form-email'], FILTER_VALIDATE_EMAIL)) {

                $check_email = "SELECT id
                FROM user 
                WHERE email = :email";
                
                $check_email_query = $this->db->prepare($check_email);
                $check_email_query->execute(array(':email' => $_POST['register-form-email']));
                $row = $check_email_query->fetch(PDO::FETCH_ASSOC);
                
                // Check if flag was returned
                if (isset($row['id'])) {
                    // Add messag
                    $this->addMessage('Her gikk visst noe galt...', 'danger');

                    // Redirect
                    $this->redirect('profil/innstillinger');
                }
                else {
                    // Store new email
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
                }
            }

            // Check if we sould update nick
            if (isset($_POST['register-form-nick'])) {
                $update_user_nick = "UPDATE user
                SET nick = :nick
                WHERE id = :user";

                $update_user_nick_query = $this->db->prepare($update_user_nick);
                $update_user_nick_query->execute(array(':nick' => $_POST['register-form-nick'],
                                                       ':user' => $this->user->getId()));
            }

            if (!$error) {
                // Add messag
                $this->addMessage('Dine endringer er lagret!', 'success');

                // Redirect
                $this->redirect('profil/innstillinger');
            }
            else {
                // Add messag
                $this->addMessage('Her gikk visst noe galt...', 'danger');

                // Redirect
                $this->redirect('profil/innstillinger');
            }
        }
        else {
            // Add messag
            $this->addMessage('Her gikk visst noe galt...', 'danger');

            // Redirect
            $this->redirect('profil/innstillinger');
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

//
// Return the class name
//

return 'ProfileController';
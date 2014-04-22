<?php
/*
 * File: authController.php
 * Holds: The AuthController-class
 * Created: 14.04.14
 * Last updated: 22.04.14
 * Project: Youkok2
 * 
*/

//
// The AuthController class
//

class AuthController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);

        // Reset menu
        $this->template->assign('HEADER_MENU', null);

        // Check query
        if ($_GET['q'] == 'logg-inn') {
            // Check if submitted
            if (isset($_POST['login2-email'])) {
                // Change post vars
                $_POST['login-email'] = $_POST['login2-email'];
                $_POST['login-pw'] = $_POST['login2-pw'];

                // Call method
                $this->logIn();
            }
            else {
                $this->displayAndCleanup('login.tpl');
            }
        }
        else if ($_GET['q'] == 'logg-ut') {
        	$this->logOut();
        }
        else if ($_GET['q'] == 'registrer') {
            $this->register();
        }
        else if ($_GET['q'] == 'glemt-passord') {
            $this->forgottenPassword();
        }
        else if ($_GET['q'] == 'nytt-passord') {
            $this->forgottenPasswordNew();
        }
        else {
            // Page not found!
        	$this->display404();
        }
    }

    //
    // Method for logging user out
    //

    private function logOut() {
        // Check if logged in
        if ($this->user->isLoggedIn()) {
            unset($_SESSION['youkok2']);
            setcookie('youkok2', null, time() - (60 * 60 * 24), '/');

            // Set message
            $this->addMessage('Du har nå logget ut.', 'success');
        }

        // Redirect to frontpage
        $this->redirect('');
    }

    //
    // Method for registering a user
    //

    private function register() {
        if ($this->user->isLoggedIn()) {
            // Wtf, redirect
            $this->redirect('');
        }
        else {
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

                $should_error = false;

                // Check if error
                if ($err) {
                    $should_error = true;
                }
                else {
                    // No errors, check unique email
                    $check_email = "SELECT id
                    FROM user 
                    WHERE email = :email";
                    
                    $check_email_query = $this->db->prepare($check_email);
                    $check_email_query->execute(array(':email' => $_POST['register-form-email']));
                    $row = $check_email_query->fetch(PDO::FETCH_ASSOC);
                    
                    // Check if flag was returned
                    if (isset($row['id'])) {
                        $should_error = true;
                    }
                    else {
                        // Check passwords
                        if ($_POST['register-form-password1'] == $_POST['register-form-password2']) {
                            // Match, create new password
                            $hash_salt = md5(rand(0, 10000000000)) . "-" . md5(time()) . "DHGDKJDHGkebabSJHingridvoldKEfggfgf";
                            $hash = $this->hashPassword($_POST['register-form-password1'], $hash_salt);
                            
                            // Insert to database
                            $create_user = "INSERT INTO user
                            (email, password, salt, nick)
                            VALUES (:email, :password, :salt, :nick)";
                            
                            $create_user_query = $this->db->prepare($create_user);
                            $create_user_query->execute(array(':email' => $_POST['register-form-email'],
                                ':password' => $hash,
                                ':salt' => $hash_salt,
                                ':nick' => $_POST['register-form-nick']));
                        }
                        else {
                            $should_error = true;
                        }
                    }
                }

                // Check if there was any errors during the signup
                if ($should_error) {
                    // Add message
                    $this->addMessage('Her gikk visst noe galt...', 'danger');
                   
                    // Redirect
                    $this->redirect('registrer');
                }
                else {
                    // Add message
                    $this->addMessage('Velkommen til Youkok2!', 'success');

                    // Log in (only session)
                    $this->setLogin($hash, $_POST['register-form-email']);

                    $this->redirect('');
                }
            }
        }
    }

    //
    // Method for forgotten password
    //

    private function forgottenPassword() {
        // Check if offline
        if ($this->user->isLoggedIn()) {
            $this->redirect('');
        }
        else {
            if (isset($_POST['forgotten-email'])) {
                // Handle stuff here
                $get_login_user = "SELECT id
                FROM user 
                WHERE email = :email";
                
                $get_login_user_query = $this->db->prepare($get_login_user);
                $get_login_user_query->execute(array(':email' => $_POST['forgotten-email']));
                $row = $get_login_user_query->fetch(PDO::FETCH_ASSOC);

                // Check result
                if (isset($row['id'])) {
                    // Create hash
                    $hash = $this->hashPassword(md5(rand(0, 100000) . md5(time()) . $row['id']), sha1(rand(0, 1000)), false);

                    // Create database entry
                    $insert_changepassword = "INSERT INTO changepassword
                    (user, hash, timeout) 
                    VALUES (:user, :hash, NOW() + INTERVAL 1 DAY)";
                    
                    $insert_changepassword_query = $this->db->prepare($insert_changepassword);
                    $insert_changepassword_query->execute(array(':user' => $row['id'], ':hash' => $hash));

                    // Send e-mail
                    $message = "Hei\n\nKlikk på linken for å kunne endre nytt passord:\n" . SITE_URL_FULL . "nytt-passord?hash=" . $hash . "\n\nMvh\nYoukok2";
                    mail($_POST['forgotten-email'], 'Glemt passord på Youkok2', $message);

                    // Add message
                    $this->addMessage('Det er blitt sendt en e-post til deg. Denne inneholder en link for å velge nytt passord. Denne linken er gyldig i 24 timer.', 'success');
                }
                else {
                    $this->addMessage('E-posten du oppga ble ikke funnet i systemet. Prøv igjen.', 'danger');
                }

                // Redirect back to form
                $this->redirect('glemt-passord');
            }
            else {
                $this->displayAndCleanup('forgotten_password.tpl');
            }
        }
    }

    //
    // Method for changing password
    //

    private function forgottenPasswordNew() {
        // Check if offline
        if ($this->user->isLoggedIn()) {
            $this->redirect('');
        }
        else {
            // Check if changepassword was found
            $validate_hash = "SELECT id, user
            FROM changepassword 
            WHERE hash = :hash
            AND timeout > NOW()";
            
            $validate_hash_query = $this->db->prepare($validate_hash);
            $validate_hash_query->execute(array(':hash' => $_GET['hash']));
            $row = $validate_hash_query->fetch(PDO::FETCH_ASSOC);

            // Check if valid or not
            if (isset($row['id'])) {
                // Check if submitted
                if (!isset($_POST['forgotten-password-new-form-password1'])) {
                    // Display
                    $this->displayAndCleanup('forgotten_password_new.tpl');
                }
                else {
                    if ($_POST['forgotten-password-new-form-password1'] == $_POST['forgotten-password-new-form-password2']) {
                        // Get salt
                        $get_user_salt = "SELECT salt, email
                        FROM user 
                        WHERE id = :user";
                        
                        $get_user_salt_query = $this->db->prepare($get_user_salt);
                        $get_user_salt_query->execute(array(':user' => $row['user']));
                        $row2 = $get_user_salt_query->fetch(PDO::FETCH_ASSOC);

                        // Check if user was found
                        if (isset($row2['salt'])) {
                            // Generate new hash
                            $hash = $this->hashPassword($_POST['forgotten-password-new-form-password1'], $row2['salt']);
                            
                            // Insert
                            $insert_user_new_password = "UPDATE user
                            SET password = :password
                            WHERE id = :user";
                            
                            $insert_user_new_password_query = $this->db->prepare($insert_user_new_password);
                            $insert_user_new_password_query->execute(array(':password' => $hash, ':user' => $row['user']));

                            // Delete from changepassword
                            $delete_changepassword = "DELETE FROM changepassword
                            WHERE user = :user";
                            
                            $delete_changepassword_query = $this->db->prepare($delete_changepassword);
                            $delete_changepassword_query->execute(array(':user' => $row['user'])); 

                            // Add message
                            $this->addMessage('Passordet er endret!', 'success');

                            // Log in (only session)
                            $this->setLogin($hash, $row2['email']);

                            $this->redirect('');
                        }
                        else {
                            // Add error message
                            $this->addMessage('Her gikk visst noe galt...', 'danger');

                            // Redirect
                            $this->redirect('');
                        }
                    }
                    else {
                        // Add error message
                        $this->addMessage('De to passordene er ikke like.', 'danger');

                        // Redirect
                        $this->redirect('nytt-passord?hash=' . $_GET['hash']);
                    }
                }
            }
            else {
                // Add error message
                $this->addMessage('Denne linken er ikke lenger gyldig.', 'danger');

                // Redirect
                $this->redirect('');
            }
        }
    }
}

//
// Loading the class-name dynamically and creating an instance doing our magic
//

// Getting the current file-path
$path = explode('/', __FILE__);

// Including the run-script to execute it all
include_once 'run.php';
?>
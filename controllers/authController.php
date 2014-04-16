<?php
/*
 * File: authController.php
 * Holds: The AuthController-class
 * Created: 14.04.14
 * Last updated: 15.04.14
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

        // Check query
        if ($_GET['q'] == 'logg-inn') {
            if (isset($_POST['login2-email'])) {
                //
            }
            else {
                $this->logIn();
            }
        }
        else if ($_GET['q'] == 'logg-ut') {
        	$this->logOut();
        }
        else if ($_GET['q'] == 'registrer') {
            $this->register();
        }
        else {
            // Page not found!
        	$this->display404();
        }
    }

    //
    // Method for logging user in
    //

    private function logIn() {
    	// Check if logged in
    	if ($this->user->isLoggedIn()) {
    		// Just redirect, should be here
            $this->redirect('');
    	}
    	else {
    		// Okey
    		if (isset($_POST['login-email']) and isset($_POST['login-pw'])) {
    			// Try to fetch email
		        $get_login_user = "SELECT id, email, salt, password
		        FROM user 
		        WHERE email = :email";
		        
		        $get_login_user_query = $this->db->prepare($get_login_user);
		        $get_login_user_query->execute(array(':email' => $_POST['login-email']));
		        $row = $get_login_user_query->fetch(PDO::FETCH_ASSOC);

		        // Check result
		        if (isset($row['id'])) {
		        	// Try to match password
		        	$hash = $this->hashPassword($_POST['login-pw'], $row['salt']);
		        	
		        	// Try to match with password from the database
		        	if ($hash === $row['password']) {
		        		// Check remember me
		        		if (isset($_POST['login-remember']) and $_POST['login-remember'] == 'pizza') {
                            $remember_me = true;
                        }
                        else {
                            $remember_me = true;
                        }

                        // Set login
                        $this->setLogin($hash, $_POST['login-email'], $remember_me);

                        // Add message
                        $this->addMessage('Du er nå logget inn.', 'success');

                        // Unset post
                        unset($_POST);

		        		// Redirect
		        		$this->redirect('');
		        	}
		        	else {
                        // Message
		        		$this->addMessage('Oisann. Feil brukernavn og/eller passord, er jeg redd. Prøv igjen. <a href="glemt-passord">Om du ikke har glemt passordet ditt, da kan du klikke her</a>.', 'danger');
		        	    
                        // Unset post
                        unset($_POST);

                        // Display
                        $this->displayAndCleanup('login.tpl');
                    }
		        }
		        else {
		        	// Message
                    $this->addMessage('Oisann. Feil brukernavn og/eller passord, er jeg redd. Prøv igjen. <a href="glemt-passord">Om du ikke har glemt passordet ditt, da kan du klikke her</a>.', 'danger');
                    
                    // Unset post
                    unset($_POST);

                    // Display
                    $this->displayAndCleanup('login.tpl');
		        }
    		}
    		else {
    			// Not submitted or anything, just redirect
                $this->redirect('');
    		}
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
    // Hash password
    //

    private function hashPassword($pass, $salt) {
        return password_fuckup(password_hash($pass, PASSWORD_BCRYPT, array('cost' => 12, 'salt' => $salt)));
    }

    private function setLogin($hash, $email, $cookie = false) {
        $strg = $email . 'asdkashdsajheeeeehehdffhaaaewwaddaaawww' . $hash;
        if ($cookie) {
            setcookie('youkok2', $strg, time() + (60 * 60 * 24 * 31), '/');
        }
        else {
            $_SESSION['youkok2'] = $strg;
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
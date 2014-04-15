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
		        	$hash = password_fuckup(password_hash($_POST['login-pw'], PASSWORD_BCRYPT, array('cost' => 12, 'salt' => $row['salt'])));
		        	
		        	// Try to match with password from the database
		        	if ($hash === $row['password']) {
		        		// Create cookie
		        		$strg = $_POST['login-email'] . 'asdkashdsajheeeeehehdffhaaaewwaddaaawww' . $hash;
		        		if (isset($_POST['login-remember']) and $_POST['login-remember'] == 'pizza') {
		        			setcookie('youkok2', $strg, time() + (60 * 60 * 24 * 31), '/');
		        		}
		        		else {
		        			$_SESSION['youkok2'] = $strg;
		        		}

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
}

//
// Loading the class-name dynamically and creating an instance doing our magic
//

// Getting the current file-path
$path = explode('/', __FILE__);

// Including the run-script to execute it all
include_once 'run.php';
?>
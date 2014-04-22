<?php
/*
 * File: profileController.php
 * Holds: The ProfileController-class
 * Created: 02.10.13
 * Last updated: 22.04.14
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

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);
        
        // Check if online
        if ($this->user->isLoggedIn()) {
        	if ($_GET['q'] == 'profil/innstillinger') {
        		if (!isset($_POST['source'])) {
        			// Assign email
		        	$this->template->assign('PROFILE_USER_EMAIL', $this->user->getEmail());

		        	// Assign other stuff
		        	if ($this->user->isVerified()) {
		        		$this->template->assign('PROFILE_USER_VERIFIED', 1);
		        	}
		        	else {
		        		$this->template->assign('PROFILE_USER_VERIFIED', 0);
		        	}

		        	if ($this->user->isBanned()) {
		        		$this->template->assign('PROFILE_USER_ACTIVE', 0);
		        	}
		        	else {
		        		$this->template->assign('PROFILE_USER_ACTIVE', 1);
		        	}

		        	// Displaying and cleaning up
		        	$this->displayAndCleanup('profile_settings.tpl');
        		}
        		else {
        			if ($_POST['source'] == 'password') {
        				$this->profilePassword();
        			}
        			else {
        				$this->redirect('');
        			}
        		}
	        	
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
    	if ($this->user->isLoggedIn() and isset($_POST['forgotten-password-new-form-oldpassword']) and isset($_POST['forgotten-password-new-form-password1']) and isset($_POST['forgotten-password-new-form-password2'])) {
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
		        	$hash_pre = $this->hashPassword($_POST['forgotten-password-new-form-oldpassword'], $row2['salt']);
		        	
		        	// Check if the old password matches the old one
		        	if ($hash_pre == $row2['password']) {
		        		// Generate new hash
			            $hash = $this->hashPassword($_POST['forgotten-password-new-form-password1'], $row2['salt']);
			            
			            // Insert
			            $insert_user_new_password = "UPDATE user
			            SET password = :password
			            WHERE id = :user";
			            
			            $insert_user_new_password_query = $this->db->prepare($insert_user_new_password);
			            $insert_user_new_password_query->execute(array(':password' => $hash, ':user' => $this->user->getId()));

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
			            $this->setLogin($hash, $this->user->getEmail(), $set_login_cookie);

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
}

//
// Loading the class-name dynamically and creating an instance doing our magic
//

// Getting the current file-path
$path = explode('/', __FILE__);

// Including the run-script to execute it all
include_once 'run.php';
?>
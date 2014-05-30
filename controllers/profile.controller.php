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

		        	// For info
		        	$this->template->assign('PROFILE_USER_EMAIL', $this->user->getEmail());
		        	$this->template->assign('PROFILE_USER_NICK', $this->user->getNick());
		        	$this->template->assign('PROFILE_USER_NTNU', $this->user->getNTNUEmail() . '@stud.ntnu.no');

		        	// Displaying and cleaning up
                    $this->template->assign('SITE_TITLE', 'Mine innstillinger');
		        	$this->displayAndCleanup('profile_settings.tpl');
        		}
        		else {
        			if ($_POST['source'] == 'password') {
        				$this->profilePassword();
        			}
        			else if ($_POST['source'] == 'verify') {
        				$this->profileVerify();
        			}
        			else if ($_POST['source'] == 'info') {
        				$this->profileInfo();
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

	//
    // Method for verifying
    //

    private function profileVerify() {
    	if ($this->user->isLoggedIn() and $this->user->isVerified() == false and isset($_POST['verify-username'])) {
    		// Create hash
    		$hash_salt = md5(rand(0, 10000000000)) . "-" . md5(time()) . "niggernigerrerer";
            $hash = $this->hashPassword(md5(rand(0, 10000000000) . 'DKHJDHDDHGDHGJDGHJDGHJDGHJ'), $hash_salt);

            // Send mail
            $mail = new PHPMailer;
            $mail->From = 'donotreply@' . SITE_DOMAIN;
            $mail->FromName = 'Youkok2';
            $mail->addAddress($_POST['verify-username'] . '@stud.ntnu.no');
            $mail->addReplyTo(SITE_EMAIL_CONTACT);

            $mail->WordWrap = 75;
            $mail->isHTML(false);

            $mail->Subject = utf8_decode('Verifiser din bruker på Youkok2');
            $message = utf8_decode(file_get_contents($this->basePath . '/mail/verify.txt'));
            $message_keys = array(
                '{{SITE_URL}}' => SITE_URL_FULL,
                '{{HASH}}' => $hash);
            $mail->Body = str_replace(array_keys($message_keys), $message_keys, $message);
            $mail->send();

            // Insert to database
            $insert_verify = "INSERT INTO verify
            (user, hash, username) 
            VALUES (:user, :hash, :username)";
            
            $insert_verify_query = $this->db->prepare($insert_verify);
            $insert_verify_query->execute(array(':user' => $this->user->getId(), ':hash' => $hash, ':username' => $_POST['verify-username']));

            // Add messag
    		$this->addMessage('En e-post er sendt til ' . $_POST['verify-username'] . '@stud.ntnu.no. Her finnes en link for å verifisere din bruker.', 'success');

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

    //
    // Update profile info
    //

    private function profileInfo() {
    	if ($this->user->isLoggedIn() and isset($_POST['register-form-email']) and isset($_POST['register-form-nick'])) {
    		// Check if we should update e-mail
    		if ($_POST['register-form-email'] != $this->user->getEmail() and strlen($_POST['register-form-email']) > 0 and filter_var($_POST['register-form-email'], FILTER_VALIDATE_EMAIL)) {
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
		            $update_user_email_query->execute(array(':email' => $_POST['register-form-email'], ':user' => $this->user->getId()));
                }
    		}

    		// Check if we sould update nick
    		if (isset($_POST['register-form-nick'])) {
    			$update_user_nick = "UPDATE user
	            SET nick = :nick
	            WHERE id = :user";
	            
	            $update_user_nick_query = $this->db->prepare($update_user_nick);
	            $update_user_nick_query->execute(array(':nick' => $_POST['register-form-nick'], ':user' => $this->user->getId()));
    		}

    		// Add messag
    		$this->addMessage('Dine endringer er lagret!', 'success');

	        // Redirect
	        $this->redirect('profil/innstillinger');
    	}
    	else {
    		// Add messag
    		$this->addMessage('Her gikk visst noe galt...22', 'danger');

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
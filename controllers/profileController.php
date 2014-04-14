<?php
/*
 * File: profileController.php
 * Holds: The ProfileController-class
 * Created: 02.10.13
 * Last updated: 05.12.13
 * Project: Youkok2
 * 
*/

//
// The REST-class doing most of the magic
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

	        	$this->template->display('profile_settings.tpl');
	        }
	        else {
	        	// TOOD
	        }
        }
        else {
        	$this->redirect('');
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
<?php
/*
 * File: processor.controller.php
 * Holds: The ProcessorController-class
 * Created: 14.04.14
 * Project: Youkok2
 * 
*/

//
// ProcessorController handles processes of ajax requests
//

class ProcessorController extends Youkok2 {

    //
    // The constructor for this subclass
    //

    public function __construct($routes) {
        // Calling Base' constructor
        parent::__construct($routes);

        // Set header to json
        header('Content-type: application/json');

        // Variable for storing the json array
        $response = array();

        // Check what we got
        if ($this->queryGetSize() < 2) {
        	$response['code'] = 500;
        }
        else {
        	// Flag
        	if ($this->queryGet(1) == 'flag') {
                // Everything to do with flags
        		if ($this->queryGet(2) == 'get') {
                    // Get flag
        			$response = $this->flagGet();
        		}
        		else if ($this->queryGet(2) == 'vote') {
                    // Vote flag
        			$response = $this->flagVote();
        		}
                else if ($this->queryGet(2) == 'name') {
                    // New flag - new name
                    $response = $this->flagName();
                }
                else if ($this->queryGet(2) == 'delete') {
                    // New flag - delete
                    $response = $this->flagDelete();
                }
        	}
            else if ($this->queryGet(1) == 'favorite') {
                // Everything to do with favorite
                if ($this->queryGet(2) == 'add') {
                    // Add favorite
                    $response = $this->favorite(true);
                }
                else {
                    // Remove favorite
                    $response = $this->favorite(false);
                }
            }
            else if ($this->queryGet(1) == 'popular') {
                // Everything to do with frontpage ajax calls
                if ($this->queryGet(2) == 'update') {
                    // Update populare download delta
                    $response = $this->homePopularUpdate();
                }
            }
            else if ($this->queryGet(1) == 'create') {
                // Everything to do with creating stuff
                if ($this->queryGet(2) == 'folder') {
                    // New folder
                    $response = $this->createFolder();
                }
                else {
                    // New file
                    $response = $this->createFile();
                }
            }
            else if ($this->queryGet(1) == 'report') {
                // Reports
                if ($this->queryGet(2) == 'send') {
                    // Send new report
                    $response = $this->reportSend();
                }
            }
            else if ($this->queryGet(1) == 'register') {
                // Ajax call for register
                if ($this->queryGet(2) == 'check') {
                    // Check if email is already in use in the system
                    $response = $this->registerCheckDuplicate();
                }
            }
            else if ($this->queryGet(1) == 'history') {
                // Ajax calls for fetching histories
                if ($this->queryGet(2) == 'get') {
                    // Fetch history
                    $response = $this->historyGet();
                }
            }
            else {
                // Not found
                $response['code'] = 500;
            }
        }

        // Return the content
        echo json_encode($response);
    }

    //
    // Method for getting flags for a file
    //

    private function flagGet() {
    	$response = array();

    	// Check stuff
    	if (isset($_POST['id']) and is_numeric($_POST['id'])) {
        	// Valid id, try to load the object
        	$item = new Item($this);
        	$item->createById($_POST['id']);
        	
            // Check if valid id
            if ($item->wasFound()) {
            	// Good to go
            	$response['code'] = 200;

            	// Load all flags
            	$flags = $item->getFlags();

            	$response['html'] = '';

            	// Loop and create html
            	if (count($flags) > 0) {
            		// Flags
            		foreach ($flags as $k => $v) {
            			$response['html'] .= $this->drawFlag($k, $v, $item);
            		}
            	}

                if ($response['html'] == '') {
                    $response['html'] = '<p>Ingen flagg!</p>';
                }
            }
            else {
            	// Invalid item
            	$response['code'] = 500;
            }
        }
        else {
        	// Invalid id
        	$response['code'] = 500;
        }

        // Return
        return $response;
    }

    //
    // Method for drawing each flag
    //

    private function drawFlag($k, $flag, $element) {
        // Load votes
        $flag->getVotes();

        // Fetch vote information
        $has_voted = $flag->userHasVoted();
        $user_vote = $flag->getUserVotedValue();
        $num_voted = $flag->getVoteValues();
        $percent = $flag->getVoteProgressPercent();

        // Variables for the markup
        $display_buttons = false;
        $additional = '';

        // Handles for different special flags
        if ($flag->getType() != 0) {
            // Some variables
            $additional_inner = '';
            $data = $flag->getData();
            
            // Fix flag independent
            if ($flag->getType() == 1) {
                // Change name
                $additional = '<hr /><p>Nåværende navn: ' . $element->getName() . '</p>';
                $additional .= '<p>Endres til: ' . $data['name'] . '</p>';
            }

            // Add comment
            if ($data['comment'] == '') {
                $additional_inner .= '<p><em>Ingen kommentar</em></p>';
            }
            else {
                
                $additional_comment = explode("<br />", nl2br($data['comment']));
                foreach ($additional_comment as $v) {
                    if ($v != '') {
                        $additional_inner .= '<p>' . $v . '</p>';
                    }
                }
                
            }
            $additional .= '<div class="givegrayaround">' . $additional_inner . '</div>';
        }

        // Check if status
        if ($this->user->isLoggedIn()) {
            // User is logged in
            if ($this->user->canContribute()) {
                // Check if user is the owner
                if ($this->user->getId() == $flag->getUser()) {
                    $question_status = '<i class="fa fa-question" title="Stemme kan ikke avlegges."></i>';
                    $question_status_bottom = '<span style="color: red;">Dette er et flagg opprettet av deg, du kan derfor <em>ikke</em> stemme.</span>';
                }
                else {
                    $display_buttons = true;
                    if ($has_voted == false) {
                        $question_status = '<i class="fa fa-question" title="Stemme ikke avlagt."></i>';
                        $question_status_bottom = 'Du har <em>ikke</em> avgitt din stemme.';
                    }
                    else {
                        if ($user_vote == false) {
                            $question_status = '<i class="fa fa-times" style="color: red;" title="Stemt for avvisning."></i>';
                            $question_status_bottom = '<span style="color: red;">Du har stemt for avvisning!</span>';
                        }
                        else {
                            $question_status = '<i class="fa fa-check" style="color: green;" title="Stemt for godkjenning."></i>';
                            $question_status_bottom = '<span style="color: green;">Du har stemt for godkjenning!</span>';
                        }
                    }
                }
            }
            else {
                if (!$this->user->hasKarma()) {
                    $question_status = '<i class="fa fa-question" title="Din konto er stengt."></i>';
                    $question_status_bottom = '<span style="color: red;">Du kan ikke lenger bidra på denne siden fordi din karma er <strong>0</strong>.</span>';
                }
                else if ($this->user->isBanned()) {
                    $question_status = '<i class="fa fa-question" title="Din konto er stengt."></i>';
                    $question_status_bottom = '<span style="color: red;">Du kan ikke lenger bidra på denne siden fordi du er bannet.</span>';
                }
                else {
                    $question_status = '<i class="fa fa-question" title="Registrer din NTNU-epost for å stemme."></i>';
                    $question_status_bottom = 'Registrer din NTNU-epost for å stemme.';
                }
            }
        }
        else {
            // User is not logged in
            $question_status = '<i class="fa fa-question" title="Logg inn og registrer din NTNU-epost for å stemme."></i>';
            $question_status_bottom = 'Logg inn og registrer din NTNU-epost for å stemme.';
        }

        // Check how many votes we need to close
        if ($flag->getType() == 0) {
            $needed_num_votes = Flag::$VotesNeededAccepted;
        }
        else {
            $needed_num_votes = Flag::$VotesNeeded;
        }
        
        return '<div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#flags-panel" href="#collapse' . $k . '">
                                ' . Flag::$flagType[$flag->getType()] . '
                                <div class="model-flags-collaps-info">
                                    ' . $question_status . '
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="' . $percent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $percent . '%;">
                                            ' . $percent . '%
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse' . $k . '" class="panel-collapse collapse">
                        <div class="panel-body">
                            ' . Flag::$flagText[$flag->getType()] . $additional . '
                            <hr />
                            <p>' . $num_voted . ' av ' . $needed_num_votes . ' godkjenninger. ' . $question_status_bottom . '</p>
                            ' . ($display_buttons ? '<button type="button" data-flag="' . $flag->getId() . '" data-value="1" class="btn btn-primary flag-button">Godkjenn</button> <button type="button" data-flag="' . $flag->getId() . '" data-value="0" class="btn btn-danger flag-button">Avvis</button>' : '') . '
                        </div>
                    </div>
                </div>';
    }

    //
    // Method for voting on a flag
    //

    private function flagVote() {
    	$response = array();
    	
    	// First, check if logged in
    	if ($this->user->isLoggedIn() and $this->user->canContribute()) {
    		// Can vote
    		if (isset($_POST['id']) and is_numeric($_POST['id']) and isset($_POST['flag']) and 
                is_numeric($_POST['flag']) and ($_POST['value'] == 1 or $_POST['value'] == 0)) {
	        	// Valid id, try to load the object
	        	$item = new Item($this);
                $item->createById($_POST['id']);
                
                // Check if valid id
                if (!$item->wasFound()) {
	            	// WTF
	            	$response['code'] = 500;
	            }
	            else {
                    // Init new flag
	            	$flag = new Flag($this);

		            // Check if flag was returned
		            if (!$flag->createById($_POST['flag'])) {
		            	$response['code'] = 500;
		            }
		            else {
		            	if ($flag->isActive() and $flag->getUser() != $this->user->getId() and 
                            $flag->getFile() == $item->getId()) {
		            		// Flag returned and is currently active, check if has voted
		            		$flag_votes = $flag->getVotes();
				            
				            // Check if has voted
				             if (!$flag->userHasVoted()) {
				            	// Insert
                                $vote = new Vote($this);
                                $vote->setUser($this->user->getId());
                                $vote->setFlag($_POST['flag']);
                                $vote->setValue($_POST['value']);
                                $vote->createNew();

                                // Find the correct values
                                if ($flag->getType() == 0) {
                                    // Voted for accepting
                                    if (!$item->isDirectory()) {
                                        $add_history = array('key' => 3, 'karma' => 1);
                                    }
                                    else {
                                        $add_history = array('key' => 4, 'karma' => 1);
                                    }
                                }
                                else if ($flag->getType() == 1) {
                                    // Vote for rename
                                    $add_history = array('key' => 5, 'karma' => 1);
                                }
                                else if ($flag->getType() == 2) {
                                    // Vote for delete
                                    $add_history = array('key' => 6, 'karma' => 2);
                                }
                                else if ($flag->getType() == 2) {
                                    // Vote for move
                                    $add_history = array('key' => 7, 'karma' => 1);
                                }

                                // Add history
                                $this->addHistory($this->user->getId(), $_POST['id'],
                                                  $_POST['flag'], $add_history['key'], null, $add_history['karma']);
                                
                                // Add karma to pending
                                $this->user->addPendingKarma($add_history['karma']);
				            }
				            else {
				            	// Update
                                $vote = $flag->getCurrentUserVote();
				            	$vote->setValue($_POST['value']);
                                $vote->updateVote();
				            }

				            $response['code'] = 200;

				            // Check for completed vote!
				            new Executioner($this, $flag);
		            	}
		            	else {
		            		$response['code'] = 500;
		            	}
		            }
	            }
	        }
	        else {
	        	$response['code'] = 500;
	        }
    	}
    	else {
    		$response['code'] = 500;
    	}

    	// Return
    	return $response;
    }

    //
    // Method for adding or removing a favorite
    //

    private function favorite($b) {
        $response = array();

        // Check stuff
        if (isset($_POST['id']) and is_numeric($_POST['id'])) {
            // Valid id, try to load the object
            $item = new Item($this);
            $item->createById($_POST['id']);

            if ($item->wasFound()) {
                // Check if logged in
                if ($this->user->isLoggedIn()) {
                    $response['code'] = 200;

                    if ($b and $item->isFavorite($this->user) == 0) {
                        $this->user->addFavorite($item);
                    
                        $response['msg'] = array(
                            array(
                                'text' => $item->getName() . ' er langt til i dine favoritter.',
                                'type' => 'success'));
                    }
                    else if (!$b) {
                        // Remove favorite
                        $this->user->removeFavorite($item);
                        
                        $response['msg'] = array(
                            array(
                                'text' => $item->getName() . ' er fjernet fra dine favoritter.',
                                'type' => 'success'));
                    }

                    $response['status'] = $b;
                }
                else {
                    $response['code'] = 500;
                }
            }
        }
        else {
            $response['code'] = 500;
        }

        return $response;
    }

    //
    // Method for updating home user delta choice
    //

    private function homePopularUpdate() {
        $response = array();

        // Update preference
        if (isset($_POST['delta']) and is_numeric($_POST['delta']) and $_POST['delta'] >= 0 and $_POST['delta'] <= 4) {
            $response['code'] = 200;

            if ($this->user->isLoggedIn()) {
                $this->user->setMostPopularDelta($_POST['delta']);
            }
            else {
                setcookie('home_popular', $_POST['delta'], time() + (60 * 60 * 24 * 7), '/');
            }          

            // Include the home controller
            include_once BASE_PATH . '/controllers/home.controller.php';

            // New instance
            $home_controller = new HomeController($this->routes, true);

            // Get service
            $response['html'] = $home_controller->getService('loadMostPopular', array($_POST['delta']));

            // Check if null
            if ($response['html'] == '') {
                $response['html'] = '<li class="list-group-item">Det er visst ingen nedlastninger i dette tidsrommet!</li>';
            }
        }
        else {
            // Invalid data
            $response['code'] = 200;
        }

        // Return
        return $response;
    }

    //
    // Method for creating folders in the system
    //

    private function createFolder() {
        $response = array();

        // Check stuff
        if (isset($_POST['id']) and is_numeric($_POST['id']) and $_POST['id'] != 1 and isset($_POST['name']) and strlen($_POST['name']) > 0) {
            if ($this->user->isLoggedIn() and $this->user->canContribute()) {
                // Trim
                $_POST['name'] = trim($_POST['name']);
                
                $item = new Item($this);
                $item->setLoadFullLocation(true);
                $item->createById($_POST['id']);
                
                // Check if valid id
                if (!$item->wasFound()) {
                    $response['code'] = 500;
                }
                else {
                    // Check if duplicates
                    $get_duplicate = "SELECT id
                    FROM archive 
                    WHERE parent = :id
                    AND name = :name";
                    
                    $get_duplicate_query = $this->db->prepare($get_duplicate);
                    $get_duplicate_query->execute(array(':id' => $_POST['id'], ':name' => $_POST['name']));
                    $row = $get_duplicate_query->fetch(PDO::FETCH_ASSOC);

                    if (isset($row['id'])) {
                        // Duplicate
                        $response['code'] = 400;
                    }
                    else {
                        // Create directory
                        $new_directory = FILE_ROOT . '/' . $item->getFullLocation() . '/' . $this->utils->generateUrlFriendly($_POST['name']) . '/';
                        mkdir($new_directory);

                        // Inser archive
                        $insert_archive = "INSERT INTO archive
                        (name, url_friendly, parent, location, is_directory)
                        VALUES (:name, :url_friendly, :parent, :location, :is_directory)";
                        
                        $insert_archive_query = $this->db->prepare($insert_archive);
                        $insert_archive_query->execute(array(':name' => $_POST['name'],
                            ':url_friendly' => $this->utils->generateUrlFriendly($_POST['name'], true),
                            ':parent' => $item->getId(),
                            ':location' => $this->utils->generateUrlFriendly($_POST['name']),
                            ':is_directory' => 1));

                        // Insert flag
                        $insert_flag = "INSERT INTO flag
                        (file, user, type)
                        VALUES (:file, :user, :type)";

                        $element_id = $this->db->lastInsertId();
                        
                        $insert_flag_query = $this->db->prepare($insert_flag);
                        $insert_flag_query->execute(array(':file' => $element_id,
                            ':user' => $this->user->getId(),
                            ':type' => 0));

                        // Add history
                        $this->addHistory($this->user->getId(), $element_id,
                                          $this->db->lastInsertId(), 1,
                                          '%u opprettet <b>' . $_POST['name'] . '</b>.',
                                          1);

                        // Add karma to pending
                        $this->user->addPendingKarma(1);

                        // Send code
                        $response['code'] = 200;

                        // Add message
                        $this->addMessage('Din mappe ble opprettet!', 'success');
                    }
                }
            }
            else {
                $response['code'] = 500;
            }
        }
        else {
            $response['code'] = 500;
        }

        // Return
        return $response;
    }

    //
    // Method for uploading files
    //

    private function createFile() {
        $response = array();

        // Check if online
        if ($this->user->isLoggedIn() and $this->user->canContribute()) {
            // Check referer
            if (isset($_SERVER['HTTP_REFERER'])) {
                // Get base
                $referer_base = str_replace(SITE_URL_FULL, '', $_SERVER['HTTP_REFERER']);

                // Create object out of the base
                $item = new Item($this);
                $item->setLoadFullLocation(true);
                $item->createByUrl($referer_base);
                $item_id = $item->getId();

                // Check if valid id
                if (is_numeric($item_id)) {
                    // Check if any files was sent
                    if (isset($_FILES['files'])) {
                        // Find what the physical location should be
                        $letters = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
                        $this_file_name = $_FILES['files']['name'][0];
                        while(true) {
                            if (file_exists(FILE_ROOT . '/' . $item->getFullLocation() . '/' . $this->utils->generateUrlFriendly($this_file_name))) {
                                $this_file_name = $letters[rand(0, count($letters) - 1)] . $this_file_name;
                            }
                            else {
                                // Gogog
                                break;
                            }
                        }
                        $upload_full_location = FILE_ROOT . '/' . $item->getFullLocation() . '/' . $this->utils->generateUrlFriendly($this_file_name);
                        
                        // Check duplicates for url friendly
                        $url_friendly = $this->utils->generateUrlFriendly($_FILES['files']['name'][0], true);
                        $num = 2;
                        while (true) {
                            $get_duplicate = "SELECT id
                            FROM archive 
                            WHERE parent = :id
                            AND url_friendly = :url_friendly";
                            
                            $get_duplicate_query = $this->db->prepare($get_duplicate);
                            $get_duplicate_query->execute(array(':id' => $item->getId(), ':url_friendly' => $url_friendly));
                            $row_duplicate = $get_duplicate_query->fetch(PDO::FETCH_ASSOC);
                            if (isset($row_duplicate['id'])) {
                                $url_friendly = $this->utils->generateUrlFriendly($letters[rand(0, count($letters) - 1)] . $url_friendly);
                                $num++;
                            }
                            else {
                                // Gogog
                                break;
                            }
                        }

                        // Check duplicates for name
                        $real_name = $_FILES['files']['name'][0];
                        $name = $_FILES['files']['name'][0];
                        $num = 2;
                        while (true) {
                            $get_duplicate = "SELECT id
                            FROM archive 
                            WHERE parent = :id
                            AND name = :name";
                            
                            $get_duplicate_query = $this->db->prepare($get_duplicate);
                            $get_duplicate_query->execute(array(':id' => $item->getId(), ':name' => $name));
                            $row_duplicate = $get_duplicate_query->fetch(PDO::FETCH_ASSOC);
                            if (isset($row_duplicate['id'])) {
                                $name = $real_name . ' (' . $num . ')';
                                $num++;
                                break;
                            }
                            else {
                                // Gogog
                                break;
                            }
                        }
                        
                        // Test for missing image
                        if (file_exists(BASE_PATH . '/assets/css/lib/images/mimetypes128/' . str_replace('/', '_', $_FILES['files']['type'][0]) . '.png')) {
                            $has_missing_image = 0;
                        }
                        else {
                            $has_missing_image = 1;
                        }

                        // Insert into archive
                        $insert_archive = "INSERT INTO archive
                        (name, url_friendly, mime_type, missing_image, parent, location, size)
                        VALUES (:name, :url_friendly, :mime_type, :missing_image, :parent, :location, :size)";
                        
                        $insert_archive_query = $this->db->prepare($insert_archive);
                        $insert_archive_query->execute(array(':name' => $name,
                            ':url_friendly' => $url_friendly,
                            ':mime_type' => str_replace('/', '_', $_FILES['files']['type'][0]),
                            ':missing_image' => $has_missing_image,
                            ':parent' => $item->getId(),
                            ':location' =>  $this->utils->generateUrlFriendly($this_file_name),
                            ':size' => $_FILES['files']['size'][0]));

                        // Insert flag
                        $insert_flag = "INSERT INTO flag
                        (file, user, type)
                        VALUES (:file, :user, :type)";
                        
                        $element_id = $this->db->lastInsertId();

                        $insert_flag_query = $this->db->prepare($insert_flag);
                        $insert_flag_query->execute(array(':file' => $element_id,
                            ':user' => $this->user->getId(),
                            ':type' => 0));

                        // Move the file
                        move_uploaded_file($_FILES['files']['tmp_name'][0], $upload_full_location);
                        
                        // Add history
                        $this->addHistory($this->user->getId(), $element_id,
                                          $this->db->lastInsertId(), 2,
                                          '%u lastet opp <b>' . $name . '</b>.',
                                          5);

                        // Add karma to pending
                        $this->user->addPendingKarma(5);

                        // Send code
                        $response['code'] = 200;

                        // Add message
                        $this->addFileMessage($name);

                        // Finally, success code
                        $response['code'] = 200;
                    }
                    else {
                        $response['code'] = 500;
                    }
                }
                else {
                    $response['code'] = 500;
                }
            }
            else {
                $response['code'] = 500;
            }
        }
        else {
            $response['code'] = 500;
        }

        // Return
        return $response;
    }

    //
    // Method for filing a report in the system
    //

    private function reportSend() {
        $response = array();

        // Check stuff
        if (isset($_POST['id']) and is_numeric($_POST['id']) and isset($_POST['category']) and strlen($_POST['category']) > 3) {
            // Valid id, try to load the object
            $item = new Item($this);
            $item->createById($_POST['id']);
            $this->collection->addIfDoesNotExist($item);
            $element = $this->collection->get($_POST['id']);

            if ($element != null and $this->user->isLoggedIn()) {
                // Good to go
                $response['code'] = 200;

                // Run the query and gogo
                $insert_report = "INSERT INTO report
                (file, user, reason, comment)
                VALUES (:file, :user, :reason, :comment)";
                
                $insert_report_query = $this->db->prepare($insert_report);
                $insert_report_query->execute(array(':file' => $element->getId(),
                    ':user' => $this->user->getId(),
                    ':reason' => $_POST['category'],
                    ':comment' => (isset($_POST['text']) ? $_POST['text'] : '')));

                // Add message
                $this->addMessage('\'' . $element->getName() . '\' er rapportert til administratorene. Takk skal du ha.', 'success');
            }
            else {
                $response['code'] = 500;
            }
        }
        else {
            $response['code'] = 500;
        }

        return $response;
    }

    //
    // Method for checking duplicate email
    //

    private function registerCheckDuplicate() {
        $response = array();

        if (isset($_POST['email']) and (isset($_POST['ignore']) or !$this->user->isLoggedIn())) {
            $check_email = "SELECT id
            FROM user 
            WHERE email = :email";
            
            $check_email_query = $this->db->prepare($check_email);
            $check_email_query->execute(array(':email' => $_POST['email']));
            $row = $check_email_query->fetch(PDO::FETCH_ASSOC);
            
            // Check if flag was returned
            if (isset($row['id'])) {
                $response['code'] = 500;
            }
            else {
                $response['code'] = 200;
            }
        }
        else {
            $response['code'] = 500;
        }

        return $response;
    }

    //
    // Method for getting the current history
    //

    private function historyGet() {
        $response = array();
        $response['html'] = '';

        // Check if valid request
        if (isset($_POST['id']) and is_numeric($_POST['id'])) {
            // Get all history
            $get_history = "SELECT h.history_text, u.nick FROM archive a 
            RIGHT JOIN history AS h ON a.id = h.file
            RIGHT JOIN user AS u ON h.user = u.id
            WHERE a.parent = :id
            AND h.history_text IS NOT NULL
            ORDER BY h.added DESC
            LIMIT 30";
            
            $get_history_query = $this->db->prepare($get_history);
            $get_history_query->execute(array(':id' => $_POST['id']));
            while ($row = $get_history_query->fetch(PDO::FETCH_ASSOC)) {
                $response['html'] .= '<p>' . str_replace('%u', (($row['nick'] == null or strlen($row['nick']) == 0) ? '<em>Anonym</em>' : $row['nick']), $row['history_text']) . '</p>';
            }

            // Check if no history
            if ($response['html'] == '') {
                $response['html'] = '<em>Ingen historikk å vise.</em>';
            }
        }
        else {
            $response['html'] = '<em>Ingen historikk å vise.</em>';
        }

        return $response;
    }

    //
    // Flagging for changing name
    //

    private function flagName() {
        $response = array();
        
        // First, check if logged in
        if ($this->user->isLoggedIn() and $this->user->canContribute()) {
            // Can vote
            if (isset($_POST['id']) and is_numeric($_POST['id']) and isset($_POST['comment']) and isset($_POST['filetype']) and isset($_POST['name']) and strlen($_POST['name'].$_POST['filetype']) > 0) {
                // Valid id, try to load the object
                $item = new Item($this);
                $item->createById($_POST['id']);
                $this->collection->addIfDoesNotExist($item);
                $element = $this->collection->get($_POST['id']);
                
                if ($element == null) {
                    // WTF
                    $response['code'] = 500;
                }
                else {
                    // Insert
                    $insert_flag = "INSERT INTO flag
                    (file, user, type, data)
                    VALUES (:file, :user, :type, :data)";
                    
                    $insert_flag_query = $this->db->prepare($insert_flag);
                    $insert_flag_query->execute(array(':file' => $_POST['id'],
                        ':user' => $this->user->getId(),
                        ':type' => 1,
                        ':data' => json_encode(array('name' => $_POST['name'].$_POST['filetype'], 'comment' => $_POST['comment']))));

                    // Code
                    $response['code'] = 200;

                    // Add message
                    $this->addMessage('Ditt flagg på \'' . $element->getName() . '\' ble opprettet. Takk for hjelpen.', 'success');
                    
                    // Delete cache
                    $this->cacheManager->deleteCache($element->getId(), 'i');
                }
            }
            else {
                $response['code'] = 500;
            }
        }
        else {
            $response['code'] = 500;
        }

        // Return
        return $response;
    }

    //
    // Flagging for deleting file
    //

    private function flagDelete() {
        $response = array();
        
        // First, check if logged in
        if ($this->user->isLoggedIn() and $this->user->canContribute()) {
            // Can vote
            if (isset($_POST['id']) and is_numeric($_POST['id']) and isset($_POST['comment'])) {
                // Valid id, try to load the object
                $item = new Item($this);
                $item->createById($_POST['id']);
                $this->collection->addIfDoesNotExist($item);
                $element = $this->collection->get($_POST['id']);
                
                if ($element == null) {
                    // WTF
                    $response['code'] = 500;
                }
                else {
                    // Insert
                    $insert_flag = "INSERT INTO flag
                    (file, user, type, data)
                    VALUES (:file, :user, :type, :data)";
                    
                    $insert_flag_query = $this->db->prepare($insert_flag);
                    $insert_flag_query->execute(array(':file' => $_POST['id'],
                        ':user' => $this->user->getId(),
                        ':type' => 2,
                        ':data' => json_encode(array('comment' => $_POST['comment']))));

                    // Code
                    $response['code'] = 200;

                    // Add message
                    $this->addMessage('Ditt flagg på \'' . $element->getName() . '\' ble opprettet. Takk for hjelpen.', 'success');
                    
                    // Delete cache
                    $this->cacheManager->deleteCache($element->getId(), 'i');
                }
            }
            else {
                $response['code'] = 500;
            }
        }
        else {
            $response['code'] = 500;
        }

        // Return
        return $response;
    }

    //
    // Method for adding a history entry
    //

    private function addHistory($user, $file, $flag, $type, $text, $karma) {
        $insert_history = "INSERT INTO history
        (user, file, flag, type, history_text, karma)
        VALUES (:user, :file, :flag, :type, :text, :karma)";
        
        $insert_history_query = $this->db->prepare($insert_history);
        $insert_history_query->execute(array(':user' => $user, 
                                             ':file' => $file,
                                             ':flag' => $flag,
                                             ':type' => $type,
                                             ':text' => $text,
                                             ':karma' => $karma));
    }
}

//
// Return the class name
//

return 'ProcessorController';
<?php
/*
 * File: processor.controller.php
 * Holds: The ProcessorController-class
 * Created: 14.04.14
 * Last updated: 13.05.14
 * Project: Youkok2
 * 
*/

//
// ProcessorController handles processes of ajax requests
//

class ProcessorController extends Base {

	//
	// A few variables
	//

	private $flagType = array('Godkjenning',
		'Endring av navn',
		'Sletting av element',
		'Flytting av element');

	private $flagText = array(
		'<p>Dette elementet er åpen for godkjenning. Dersom elementet hører til på Youkok2 gjør du en god gjerning ved å stemme for å godkjenne den, slik at andre kan dra nytte av den seinere.</p>
		<p>Om elementet skulle stride mot våre <a href="retningslinjer" target="_blank">retningslinjer</a> kan du enten stemme for å avvise den, eller, i store overtrap av reglementet, velge å <a href="hjelp" target="_blank">rapportere</a> tilfellet.</p>',

		'<p>Dette flagget er et forslag på navnendring av elementet. Dersom du syntes at denne navnendringen er en forbedring kan du velge å godkjenne den. Om dette ikke er tilfellet kan du velge å avvise forslaget.</p>',

		'<p>Dette flagget er et forslag om å permanent slette elementet. Dersom du syntes dette er på sin plass kan du stemme for å godkjenne dette forslaget, eller så kan du stemme for å avvise det.</p>
        <p>Legg merke til at misbruk av slettefunksjonen vil bli slått ned på!</p>',

		'3',);

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);

        // Set header to json
        header('Content-type: application/json');

        // Variable for storing the json array
        $response = array();

        // Catch what request is comming
        $url_fragment = explode('/', $_GET['q']);
        
        // Check what we got
        if (count($url_fragment) < 2) {
        	$response['code'] = 500;
        }
        else {
        	// Flag
        	if ($url_fragment[1] == 'flag') {
        		if ($url_fragment[2] == 'get') {
        			$response = $this->flagGet();
        		}
        		else if ($url_fragment[2] == 'vote') {
        			$response = $this->flagVote();
        		}
                else if ($url_fragment[2] == 'name') {
                    $response = $this->flagName();
                }
                else if ($url_fragment[2] == 'delete') {
                    $response = $this->flagDelete();
                }
        	}
            else if ($url_fragment[1] == 'favorite') {
                if ($url_fragment[2] == 'add') {
                    $response = $this->favorite(true);
                }
                else {
                    $response = $this->favorite(false);
                }
            }
            else if ($url_fragment[1] == 'popular') {
                if ($url_fragment[2] == 'update') {
                    $response = $this->homePopularUpdate();
                }
            }
            else if ($url_fragment[1] == 'create') {
                if ($url_fragment[2] == 'folder') {
                    $response = $this->createFolder();
                }
                else {
                    $response = $this->createFile();
                }
            }
            else if ($url_fragment[1] == 'report') {
                if ($url_fragment[2] == 'send') {
                    $response = $this->reportSend();
                }
            }
            else if ($url_fragment[1] == 'register') {
                if ($url_fragment[2] == 'check') {
                    $response = $this->registerCheckDuplicate();
                }
            }
            else if ($url_fragment[1] == 'history') {
                if ($url_fragment[2] == 'get') {
                    $response = $this->getHistory();
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
        	$item = new Item($this->collection, $this->db);
        	$item->createById($_POST['id']);
        	$this->collection->addIfDoesNotExist($item);
            $element = $this->collection->get($_POST['id']);

            if ($element != null) {
            	// Good to go
            	$response['code'] = 200;

            	// Load all flags
            	$flags = $element->getFlags();
            	$response['html'] = '';

            	// Loop and create html
            	if (count($flags) > 0) {
            		// Flags
            		foreach ($flags as $k => $v) {
            			$response['html'] .= $this->drawFlag($k, $v, $element);
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
    // Method for voting on a flag
    //

    private function flagVote() {
    	$response = array();
    	
    	// First, check if logged in
    	if ($this->user->isLoggedIn() and $this->user->canContribute()) {
    		// Can vote
    		if (isset($_POST['id']) and is_numeric($_POST['id']) and isset($_POST['flag']) and is_numeric($_POST['flag']) and ($_POST['value'] == 1 or $_POST['value'] == 0)) {
	        	// Valid id, try to load the object
	        	$item = new Item($this->collection, $this->db);
	        	$item->createById($_POST['id']);
	        	$this->collection->addIfDoesNotExist($item);
	            $element = $this->collection->get($_POST['id']);
	            
	            if ($element == null) {
	            	// WTF
	            	$response['code'] = 500;
	            }
	            else {
	            	// Get flag
	            	$get_flag = "SELECT *
		            FROM flag 
		            WHERE id = :id";
		            
		            $get_flag_query = $this->db->prepare($get_flag);
		            $get_flag_query->execute(array(':id' => $_POST['flag']));
		            $row = $get_flag_query->fetch(PDO::FETCH_ASSOC);
		            
		            // Check if flag was returned
		            if (!isset($row['id'])) {
		            	$response['code'] = 500;
		            }
		            else {
		            	if ($row['active'] == 1 and $row['user'] != $this->user->getId()) {
		            		// Flag returned and is currently active, check if has voted
		            		$get_vote = "SELECT *
				            FROM vote 
				            WHERE flag = :flag
				            AND user = :user";
				            
				            $get_vote_query = $this->db->prepare($get_vote);
				            $get_vote_query->execute(array(':flag' => $_POST['flag'], ':user' => $this->user->getId()));
				            $row2 = $get_vote_query->fetch(PDO::FETCH_ASSOC);

				            // Check if has voted
				             if (!isset($row2['id'])) {
				            	// Insert
				            	$insert_vote = "INSERT INTO vote
				            	(user, flag, value)
				            	VALUES (:user, :flag, :value)";
					            
					            $insert_vote_query = $this->db->prepare($insert_vote);
					            $insert_vote_query->execute(array(':user' => $this->user->getId(), ':flag' => $_POST['flag'], ':value' => $_POST['value']));
				            }
				            else {
				            	// Update
				            	$update_vote = "UPDATE vote
				            	SET value = :value
				            	WHERE user = :user
				            	AND flag = :flag";
					            
					            $update_vote_query = $this->db->prepare($update_vote);
					            $update_vote_query->execute(array(':value' => $_POST['value'], ':user' => $this->user->getId(), ':flag' => $_POST['flag']));
				            }

				            $response['code'] = 200;

				            // Check for completed vote!
				            new Executioner($element, $this->db, $_POST['flag']);
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
    // Method for drawing each flag
    //

    private function drawFlag($k, $flag, $element) {
    	// Some variables
    	$has_voted = false;
    	$num_voted = 0;
    	$user_vote = null;
    	$num_votes_needed = 5;
    	$display_buttons = false;
        $additional = '';

    	// Get votes
    	$get_all_votes = "SELECT *
        FROM vote
        WHERE flag = :flag";
        
        $get_all_votes_query = $this->db->prepare($get_all_votes);
        $get_all_votes_query->execute(array(':flag' => $flag['id']));
        while ($row = $get_all_votes_query->fetch(PDO::FETCH_ASSOC)) {
            // Check if voted
            if ($this->user->isLoggedIn() and $row['user'] == $this->user->getId()) {
            	$has_voted = true;
            	$user_vote = $row['value'];
            }

            // Count new vote
            if ($row['value'] == 1) {
            	$num_voted++;
            }
        }
        // Handles for different special flags
        if ($flag['type'] != 0) {
            // Some variables
            $additional_inner = '';
            $data = json_decode($flag['data'], true);
            
            // Fix flag independent
            if ($flag['type'] == 1) {
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

        // Calculate percent
        $percent = ($num_voted / 5) * 100;

    	// Check if status
    	if ($this->user->isLoggedIn()) {
    		// User is logged in
  			if ($this->user->canContribute()) {
                // Check if user is the owner
                if ($this->user->getId() == $flag['user']) {
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

    	return '<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#flags-panel" href="#collapse' . $k . '">
								' . $this->flagType[$flag['type']] . '
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
							' . $this->flagText[$flag['type']] . $additional . '
							<hr />
							<p>' . $num_voted . ' av ' . $num_votes_needed . ' godkjenninger. ' . $question_status_bottom . '</p>
							' . ($display_buttons ? '<button type="button" data-flag="' . $flag['id'] . '" data-value="1" class="btn btn-primary flag-button">Godkjenn</button> <button type="button" data-flag="' . $flag['id'] . '" data-value="0" class="btn btn-danger flag-button">Avvis</button>' : '') . '
						</div>
					</div>
				</div>';
    }

    //
    // Method for adding or removing a favorite
    //

    private function favorite($b) {
        $response = array();

        // Check stuff
        if (isset($_POST['id']) and is_numeric($_POST['id'])) {
            // Valid id, try to load the object
            $item = new Item($this->collection, $this->db);
            $item->createById($_POST['id']);
            $this->collection->addIfDoesNotExist($item);
            $element = $this->collection->get($_POST['id']);

            if ($element != null) {  
                // Check if logged in
                if ($this->user->isLoggedIn()) {
                    $response['code'] = 200;

                    if ($b and $element->isFavorite($this->user) == 0) {
                        // Add new favorite
                        $insert_favorite = "INSERT INTO favorite
                        (file, user)
                        VALUES (:file, :user)";
                        
                        $insert_favorite_query = $this->db->prepare($insert_favorite);
                        $insert_favorite_query->execute(array(':file' => $element->getId(), ':user' => $this->user->getId()));
                    
                        $response['msg'] = array(
                            array(
                                'text' => $element->getName() . ' er langt til i dine favoritter.',
                                'type' => 'success'));
                    }
                    else if (!$b) {
                        // Remove favorite
                        $remove_favorite = "DELETE FROM favorite
                        WHERE file = :file
                        AND user = :user";
                        
                        $remove_favorite_query = $this->db->prepare($remove_favorite);
                        $remove_favorite_query->execute(array(':file' => $element->getId(), ':user' => $this->user->getId()));
                        
                        $response['msg'] = array(
                            array(
                                'text' => $element->getName() . ' er fjernet fra dine favoritter.',
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
        $delta = array(' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 WEEK)', 
            ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH)', 
            ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 YEAR)', 
            '',
            ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY)');

        // Check stuff
        if (isset($_POST['delta']) and is_numeric($_POST['delta']) and $_POST['delta'] >= 0 and $_POST['delta'] <= 4) {
            $response['code'] = 200;

            if ($this->user->isLoggedIn()) {
                $update_user = "UPDATE user
                SET most_popular_delta = :delta
                WHERE id = :id";
                
                $update_user_query = $this->db->prepare($update_user);
                $update_user_query->execute(array(':delta' => $_POST['delta'], ':id' => $this->user->getId()));
            }
            else {
                $_SESSION['home_popular'] = $_POST['delta'];
            }

            // Fetch
            $response['html'] = '';
            $get_most_popular = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'
            FROM download d
            " . $delta[$_POST['delta']] . "
            GROUP BY d.file
            ORDER BY downloaded_times DESC
            LIMIT 15";
            
            $get_most_popular_query = $this->db->prepare($get_most_popular);
            $get_most_popular_query->execute();
            while ($row = $get_most_popular_query->fetch(PDO::FETCH_ASSOC)) {
                // Create new object
                $item = new Item($this->collection, $this->db);
                $item->setShouldLoadRoot(true);
                $item->createById($row['id']);

                // Add to collection if new
                $this->collection->add($item);

                // Load item from collection
                $element = $this->collection->get($row['id']);

                // Set downloaded
                $element->setDownloadCount($_POST['delta'], $row['downloaded_times']);

                // CHeck if element was loaded
                if ($element != null) {
                    $element_url = $element->generateUrl($this->paths['download'][0]);
                    $root_parent = $element->getRootParent();
                    $response['html'] .= '<li class="list-group-item"><a href="' . $element_url . '">' . $element->getName() . '</a> @ ' . ($root_parent == null ? '' : '<a href="' . $root_parent->generateUrl($this->paths['archive'][0]) . '">' . $root_parent->getName() . '</a>') . ' [' . number_format($element->getDownloadCount($_POST['delta'])) . ']</a></li>';
                }
            }
        }
        else {
            $response['code'] = 500;
        }
        
        // Check if null
        if ($response['html'] == '') {
            $response['html'] = '<li class="list-group-item">Det er visst ingen nedlastninger i dette tidsrommet!</li>';
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
                
                // Create element
                $item = new Item($this->collection, $this->db);
                $item->setShouldLoadPhysicalLocation(true);
                $item->createById($_POST['id']);

                // Add to collection if new
                $this->collection->add($item);

                // Load item from collection
                $element = $this->collection->get($_POST['id']);
                if ($element == null) {
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
                        $new_directory = $this->fileDirectory . $element->getFullLocation() . '/' . $this->generateUrlFriendly($_POST['name']);
                        mkdir($new_directory);

                        // Inser archive
                        $insert_archive = "INSERT INTO archive
                        (name, url_friendly, parent, location, is_directory)
                        VALUES (:name, :url_friendly, :parent, :location, :is_directory)";
                        
                        $insert_archive_query = $this->db->prepare($insert_archive);
                        $insert_archive_query->execute(array(':name' => $_POST['name'],
                            ':url_friendly' => $this->generateUrlFriendly($_POST['name'], true),
                            ':parent' => $element->getId(),
                            ':location' => $this->generateUrlFriendly($_POST['name']),
                            ':is_directory' => 1));

                        // Insert flag
                        $insert_flag = "INSERT INTO flag
                        (file, user, type)
                        VALUES (:file, :user, :type)";
                        
                        $insert_flag_query = $this->db->prepare($insert_flag);
                        $insert_flag_query->execute(array(':file' => $this->db->lastInsertId(),
                            ':user' => $this->user->getId(),
                            ':type' => 0));

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
                $item = new Item($this->collection, $this->db);
                $item->setShouldLoadPhysicalLocation(true);
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
                            if (file_exists($this->fileDirectory . '/' . $item->getFullLocation() . '/' . $this->generateUrlFriendly($this_file_name))) {
                                $this_file_name = $letters[rand(0, count($letters) - 1)] . $this_file_name;
                            }
                            else {
                                // Gogog
                                break;
                            }
                        }
                        $upload_full_location = $this->fileDirectory . '/' . $item->getFullLocation() . '/' . $this->generateUrlFriendly($this_file_name);
                        
                        // Check duplicates for url friendly
                        $url_friendly = $this->generateUrlFriendly($_FILES['files']['name'][0], true);
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
                                $url_friendly = $this->generateUrlFriendly($letters[rand(0, count($letters) - 1)] . $url_friendly);
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
                        if (file_exists($this->basePath . '/assets/css/lib/images/mimetypes128/' . str_replace('/', '_', $_FILES['files']['type'][0]) . '.png')) {
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
                            ':location' =>  $this->generateUrlFriendly($this_file_name),
                            ':size' => $_FILES['files']['size'][0]));

                        // Insert flag
                        $insert_flag = "INSERT INTO flag
                        (file, user, type)
                        VALUES (:file, :user, :type)";
                        
                        $insert_flag_query = $this->db->prepare($insert_flag);
                        $insert_flag_query->execute(array(':file' => $this->db->lastInsertId(),
                            ':user' => $this->user->getId(),
                            ':type' => 0));

                        // Move the file
                        move_uploaded_file($_FILES['files']['tmp_name'][0], $upload_full_location);
                        
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
            $item = new Item($this->collection, $this->db);
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

    private function getHistory() {
        $response = array();
        $response['html'] = '';

        // Check if valid request
        if (isset($_POST['id']) and is_numeric($_POST['id'])) {
            // Get all history
            $get_history = "SELECT h.history_text, u.nick FROM archive a 
            RIGHT JOIN history AS h ON a.id = h.file
            RIGHT JOIN user AS u ON h.user = u.id
            WHERE a.parent = :id
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
                $item = new Item($this->collection, $this->db);
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
                $item = new Item($this->collection, $this->db);
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
    // Generic method for generating SEO friendly urls and directory names
    //

    private function generateUrlFriendly($s, $for_url = false) {
        // Replace first here to keep "norwegian" names in a way
        $s = str_replace(array('Æ', 'Ø', 'Å'), array('ae', 'o', 'aa'), $s);
        $s = str_replace(array('æ', 'ø', 'å'), array('ae', 'o', 'aa'), $s);
        
        // Remove all non-alphanumeric, keep spaces and dots, also remove whitespace if more than one space
        $s = preg_replace('/\s\s+/', ' ', preg_replace("/[^a-z0-9 -_\.]/", '', strtolower($s)));
        
        // Decide how to deal with spaces
        if ($for_url) {
            $s = str_replace(' ', '-', $s);
        }
        else {
            $s = str_replace(' ', '_', $s);
        }
        
        return $s;
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
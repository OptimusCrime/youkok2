<?php
/*
 * File: processorController.php
 * Holds: The ProcessorController-class
 * Created: 14.04.14
 * Last updated: 14.04.14
 * Project: Youkok2
 * 
*/

//
// Todo
//

class ProcessorController extends Base {

	//
	// A few variables
	//

	private $flagType = array('Godkjenning',
		'Endring av navn',
		'Sletting av fil/mappe',
		'Flytting av fil/mappe',
		'Fjerning av duplikat');

	private $flagText = array(
		'<p>Denne fila er åpen for godkjenning. Dersom fila hører til på YouKok gjør du en god gjerning ved å stemme for å godkjenne den, slik at andre kan dra nytte av den seinere.</p>
		<p>Om fila skulle stride mot våre <a href="#">retningslinjer</a> kan du enten stemme for å avvise den, eller, i store overtrap av reglementet, velge å <a href="#">rapportere</a> tilfellet.</p>',

		'1',

		'2',

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
        	}
        }

        // Return the content
        echo json_encode($response);
    }

    //
    // TODO
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
            			$response['html'] .= $this->drawFlag($k, $v);
            		}
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
    //
    //

    private function flagVote() {
    	$response = array();
    	
    	// First, check if logged in
    	if ($this->user->isLoggedIn() and $this->user->isVerified()) {
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
		            	if ($row['active'] == 1) {
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

    private function drawFlag($k, $flag) {
    	// Some variables
    	$has_voted = false;
    	$num_voted = 0;
    	$user_vote = null;
    	$num_votes_needed = 5;
    	$display_buttons = false;

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

        // Calculate percent
        $percent = ($num_voted/5)*100;

    	// Check if status
    	if ($this->user->isLoggedIn()) {
    		// User is logged in
  			if ($this->user->isVerified()) {
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
  			else {
  				$question_status = '<i class="fa fa-question" title="Registrer din NTNU-epost for å stemme."></i>';
  				$question_status_bottom = 'Registrer din NTNU-epost for å stemme.';
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
							' . $this->flagText[$flag['type']] . '
							<hr />
							<p>' . $num_voted . ' av ' . $num_votes_needed . ' godkjenninger. ' . $question_status_bottom . '</p>
							' . ($display_buttons ? '<button type="button" data-flag="' . $flag['id'] . '" data-value="1" class="btn btn-primary flag-button">Godkjenn</button> <button type="button" data-flag="' . $flag['id'] . '" data-value="0" class="btn btn-danger flag-button">Avvis</button>' : '') . '
						</div>
					</div>
				</div>';
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
<?php
/*
 * File: flag.php
 * Holds: Class for a flag belonging to an item
 * Created: 11.05.14
 * Project: Youkok2
 * 
*/

//
// Holds a flag
//

//
// Holds a Course
//

class Flag {
    
    //
    // Some variables
    //
    
    // Pointer to the controller
    private $controller;

    // Database fields
    private $id;
    private $file;
    private $user;
    private $flagged;
    private $type;
    private $active;
    private $data;
    private $message;

    // Other stuff
    private $votes;
    private $userHasVoted;
    private $userVotedValue;
    private $voteValues;


    //
    // Information
    //

    public static $flagType = array('Godkjenning',
        'Endring av navn',
        'Sletting av element',
        'Flytting av element');

    public static $flagText = array(
        '<p>Dette elementet er åpen for godkjenning. Dersom elementet hører til på Youkok2 gjør du en god gjerning ved å stemme for å godkjenne den, slik at andre kan dra nytte av den seinere.</p>
        <p>Om elementet skulle stride mot våre <a href="retningslinjer" target="_blank">retningslinjer</a> kan du enten stemme for å avvise den, eller, i store overtrap av reglementet, velge å <a href="hjelp" target="_blank">rapportere</a> tilfellet.</p>',

        '<p>Dette flagget er et forslag på navnendring av elementet. Dersom du syntes at denne navnendringen er en forbedring kan du velge å godkjenne den. Om dette ikke er tilfellet kan du velge å avvise forslaget.</p>',

        '<p>Dette flagget er et forslag om å permanent slette elementet. Dersom du syntes dette er på sin plass kan du stemme for å godkjenne dette forslaget, eller så kan du stemme for å avvise det.</p>
        <p>Legg merke til at misbruk av slettefunksjonen vil bli slått ned på!</p>',

        '3',);
    public static $VotesNeeded = 5;
    
    //
    // Constructor
    //
    
    public function __construct($controller) {
    	// Store controller reference
    	$this->controller = &$controller;

    	// Set all fields to null first
    	$this->id = null;
    	$this->file = null;
    	$this->user = null;
    	$this->flagged = null;
    	$this->type = null;
    	$this->active = null;
    	$this->data = null;
    	$this->message = null;

        $this->votes = null;
        $this->userHasVoted = false;
        $this->userVotedValue = false;
        $this->voteValues = array(0 => 0, 1 => 0);
    }

    //
    // Setters
    //

    public function setAll($arr) {
    	// Loop all fields in the array
    	foreach ($arr as $k => $v) {
    		// Check that the field exists as a property/attribute in this class
    		if (property_exists('Flag', $k)) {
    			// Set value
    			$this->$k = $v;
    		}
    	}
    }

    public function setId($id) {
    	$this->id = $id;
    }

    public function setFile($file) {
    	$this->file = $file;
    }

    public function setUser($user) {
    	$this->user = $user;
    }

    public function setFlagged($flagged) {
    	$this->flagged = $flagged;
    }

    public function setType($type) {
    	$this->type = $type;
    }

    public function setActive($b) {
    	$this->active = $b;
    }

    public function setData($data) {
    	$this->data = $data;
    }

    public function setMessage($m) {
    	$this->message = $m;
    }

    //
    // Getters
    //

    public function getId() {
    	return $this->id;
    }

    public function getFile() {
    	return $this->file;
    }

    public function getUser() {
    	return $this->user;
    }

    public function getFlagged() {
    	return $this->flagged;
    }

    public function getType() {
    	return $this->type;
    }

    public function isActive() {
    	return $this->active;
    }

    public function getData() {
    	return json_decode($this->data, true);
    }

    public function getMessage() {
    	return $this->message;
    }

    //
    // Votes
    //

    public function getVotes() {
        // Check if votes are already fetched
        if ($this->votes == null) {
            // Init array
            $this->votes = array();

            // Load all votes
            $get_all_votes = "SELECT *
            FROM vote
            WHERE flag = :flag";
            
            $get_all_votes_query = $this->controller->db->prepare($get_all_votes);
            $get_all_votes_query->execute(array(':flag' => $this->id));
            while ($row = $get_all_votes_query->fetch(PDO::FETCH_ASSOC)) {
                // Create new flag
                $vote = new Vote($this->controller);

                // Set all fields
                $vote->setAll($row);

                // Add object to array
                $this->votes[] = $vote;

                // Set all information for this flag and user
                if ($this->controller->user->isLoggedIn()) {
                    if ($vote->getUser() == $this->controller->user->getId()) {
                        $this->userHasVoted = true;
                        $this->userVotedValue = $vote->getValue();
                    }
                }

                // Add vote value
                $this->voteValues[$vote->getValue()]++;
            }
        }
        
        // Return the array
        return $this->votes;
    }

    //
    // Method to check if the current user has voted or not
    //

    public function userHasVoted() {
        // Check if fetched
        if ($this->votes == null) {
            $this->getVotes();
        }

        // Return
        return $this->userHasVoted;
    }

    //
    // Method for returning what the user has voted (if he/she has voted)
    //

    public function getUserVotedValue() {
        // Check if fetched
        if ($this->votes == null) {
            $this->getVotes();
        }

        // Return
        return $this->userVotedValue;
    }

    //
    // Get vote values
    //

    public function getVoteValues($type = 1) {
        // Check if fetched
        if ($this->votes == null) {
            $this->getVotes();
        }

        // Return
        return $this->voteValues[$type];
    }

    //
    // Get progress percent
    //

    public function getVoteProgressPercent() {
        // Check if fetched
        if ($this->votes == null) {
            $this->getVotes();
        }

        return ($this->voteValues[1] / Flag::$VotesNeeded) * 100;
    }

    //
    // Return the vote object voted on by the current user
    //

    public function getCurrentUserVote() {
        // Check if fetched
        if ($this->votes == null) {
            $this->getVotes();
        }

        foreach ($this->votes as $v) {
            if ($v->getUser() == $this->controller->user->getId()) {
                return $v;
            }
        }

        return null;
    }

    //
    // Create
    //

    public function createById($id) {
        $create_flag = "SELECT *
        FROM flag
        WHERE id = :id";

        $create_flag_query = $this->controller->db->prepare($create_flag);
        $create_flag_query->execute(array(':id' => $id));
        $row = $create_flag_query->fetch(PDO::FETCH_ASSOC);

        if (!isset($row['id'])) {
            return false;
        }
        else {
            $this->setAll($row);
            return true;
        }
    }
}
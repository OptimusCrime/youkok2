<?php
/*
 * File: executioner.php
 * Holds: Executes completed flags
 * Created: 14.04.14
 * Last updated: 18.04.14
 * Project: Youkok2
 * 
*/

//
// Executioner executes the completed flags
//

class Executioner {

    //
    // Variables
    //

    private $element;
    private $db;
    private $flag;
    private $letters;

    //
    // Constructor
    //
    
    public function __construct($element, $db, $flag) {
        // Pointers
        $this->element = $element;
        $this->db = $db;
        $this->flag = $flag;

        if ($element != null) {
            $this->analyze();
        }

        $this->letters = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');                   
    }

    //
    // Analyze what kind of flag to execute
    //

    private function analyze() {
        // Get flag
        $get_flag = "SELECT *
        FROM flag 
        WHERE id = :id";
        
        $get_flag_query = $this->db->prepare($get_flag);
        $get_flag_query->execute(array(':id' => $this->flag));
        $this->flag = $get_flag_query->fetch(PDO::FETCH_ASSOC);
        
        // Check if flag was returned
        if (!isset($this->flag['id'])) {
            $response['code'] = 500;
        }
        else {
            if ($this->flag['active'] == 1) {
                // Flag okey, check if reach 5 votes
                $get_votes1 = "SELECT COUNT(id) as 'votes'
                FROM vote 
                WHERE flag = :flag
                AND value = 1";
                
                $get_votes1_query = $this->db->prepare($get_votes1);
                $get_votes1_query->execute(array(':flag' => $this->flag['id']));
                $votes1 = $get_votes1_query->fetch(PDO::FETCH_ASSOC);

                $get_votes2 = "SELECT COUNT(id) as 'votes'
                FROM vote 
                WHERE flag = :flag
                AND value = 0";
                
                $get_votes2_query = $this->db->prepare($get_votes2);
                $get_votes2_query->execute(array(':flag' => $this->flag['id']));
                $votes2 = $get_votes2_query->fetch(PDO::FETCH_ASSOC);

                // See which way to go
                $is_finished = false;
                $finished_type = null;
                if (isset($votes1['votes']) and $votes1['votes'] == 5) {
                    $is_finished = true;
                    $finished_type = true;
                }
                if (isset($votes2['votes']) and $votes2['votes'] == 5) {
                    $is_finished = true;
                    $finished_type = false;
                }

                // See if finished
                if ($is_finished == true) {
                    // Executeeeee
                    if ($this->flag['type'] == 0) {
                        $this->execute0($finished_type);
                    }
                    else if ($this->flag['type'] == 1) {
                        $this->execute1($finished_type);
                    }
                    else if ($this->flag['type'] == 2) {
                        $this->execute2($finished_type);
                    }
                }
            }
        }
    }

    //
    // Execute - Godkjenn
    //

    private function execute0($type) {
        if ($type == true) {
            // Set verified
            $update_element = "UPDATE archive
            SET is_accepted = 1
            WHERE id = :id";
        }
        else {
            // Set hidden
            $update_element = "UPDATE archive
            SET is_visible = 0
            WHERE id = :id";
        }
        
        
        $update_element_query = $this->db->prepare($update_element);
        $update_element_query->execute(array(':id' => $this->element->getId()));

        // Update flag
        $update_flag = "UPDATE flag
        SET active = 0
        WHERE id = :id";
        
        $update_flag_query = $this->db->prepare($update_flag);
        $update_flag_query->execute(array(':id' => $this->flag['id']));

        // Get all voters
        $get_all_votes = "SELECT *
        FROM vote
        WHERE flag = :flag";
        
        $votes = array();
        $get_all_votes_query = $this->db->prepare($get_all_votes);
        $get_all_votes_query->execute(array(':flag' => $this->flag['id']));
        while ($row = $get_all_votes_query->fetch(PDO::FETCH_ASSOC)) {
            $votes[] = $row;
        }

        // Loop all votes
        foreach ($votes as $v) {
            // Check what values to update
            $karma_value_string = '';
            if ($v['value'] == $type) {
                $karma_value_string = '+1';
            }
            else {
                $karma_value_string = '-1';
            }

            // Run the actual query
            $update_user_karma = "UPDATE user
            SET karma = karma" . $karma_value_string . "
            WHERE id = :id";
            
            $update_user_karma_query = $this->db->prepare($update_user_karma);
            $update_user_karma_query->execute(array(':id' => $v['user']));
        }
        
        // Update karma for the owner
        $karma_value_string = '';
        if ($this->element->isDirectory()) {
            if ($type) {
                $karma_value_string = '+1';
            }
            else {
                $karma_value_string = '-3';
            }
        }
        else {
            if ($type) {
                $karma_value_string = '+3';
            }
            else {
                $karma_value_string = '-3';
            }
        }

        // Update owner karma
        $update_user_karma = "UPDATE user
        SET karma=karma" . $karma_value_string . "
        WHERE id = :id";
        
        $update_user_karma_query = $this->db->prepare($update_user_karma);
        $update_user_karma_query->execute(array(':id' => $this->flag['user']));

        // Create history
        $this->addHistory($this->flag['user'], $this->element->getId(), '%u opprettet ' . $this->element->getName() . '.');

    }

    //
    // Execute - Navn
    //

    private function execute1($type) {
        if ($type == true) {
            $flag_data = json_decode($this->flag['data'], true);

            // Check duplicates for url friendly
            $url_friendly = $this->generateUrlFriendly($flag_data['name'], true);
            $num = 2;
            while (true) {
                $get_duplicate = "SELECT id
                FROM archive 
                WHERE parent = :id
                AND url_friendly = :url_friendly";
                
                $get_duplicate_query = $this->db->prepare($get_duplicate);
                $get_duplicate_query->execute(array(':id' => $this->element->getId(), ':url_friendly' => $url_friendly));
                $row_duplicate = $get_duplicate_query->fetch(PDO::FETCH_ASSOC);
                if (isset($row_duplicate['id'])) {
                    $url_friendly = $this->generateUrlFriendly($this->letters[rand(0, count($this->letters) - 1)] . $url_friendly);
                    $num++;
                }
                else {
                    // Gogog
                    break;
                }
            }

            // Create new name
            $update_element = "UPDATE archive
            SET name = :name,
            url_friendly = :url_friendly
            WHERE id = :id";

            $update_element_query = $this->db->prepare($update_element);
            $update_element_query->execute(array(':id' => $this->element->getId(),
                ':name' => $flag_data['name'],
                'url_friendly' => $url_friendly));
        }

        // Update flag
        $update_flag = "UPDATE flag
        SET active = 0
        WHERE id = :id";
        
        $update_flag_query = $this->db->prepare($update_flag);
        $update_flag_query->execute(array(':id' => $this->flag['id']));

        // Get all voters
        $get_all_votes = "SELECT *
        FROM vote
        WHERE flag = :flag";
        
        $votes = array();
        $get_all_votes_query = $this->db->prepare($get_all_votes);
        $get_all_votes_query->execute(array(':flag' => $this->flag['id']));
        while ($row = $get_all_votes_query->fetch(PDO::FETCH_ASSOC)) {
            $votes[] = $row;
        }

        // Loop all votes
        foreach ($votes as $v) {
            // Check what values to update
            $karma_value_string = '';
            if ($v['value'] == $type) {
                $karma_value_string = '+1';
            }
            else {
                $karma_value_string = '-1';
            }

            // Run the actual query
            $update_user_karma = "UPDATE user
            SET karma = karma" . $karma_value_string . "
            WHERE id = :id";
            
            $update_user_karma_query = $this->db->prepare($update_user_karma);
            $update_user_karma_query->execute(array(':id' => $v['user']));
        }
        
        // Update karma for the owner
        $karma_value_string = '';
        if ($type) {
            $karma_value_string = '+3';
        }
        else {
            $karma_value_string = '-2';
        }

        // Update owner karma
        $update_user_karma = "UPDATE user
        SET karma=karma" . $karma_value_string . "
        WHERE id = :id";
        
        $update_user_karma_query = $this->db->prepare($update_user_karma);
        $update_user_karma_query->execute(array(':id' => $this->flag['user']));

        // Create history
        $this->addHistory($this->flag['user'], $this->element->getId(), '%u endret navn fra <b>' . $this->element->getName() . '</b> til <b>' . $flag_data['name'] . '</b>.');
    }


    //
    // Execute - Slett
    //

    private function execute2($type) {
        if ($type == true) {
            // "Delete"
            $update_element = "UPDATE archive
            SET is_visible = 0
            WHERE id = :id";

            $update_element_query = $this->db->prepare($update_element);
            $update_element_query->execute(array(':id' => $this->element->getId()));
        }

        // Update flag
        $update_flag = "UPDATE flag
        SET active = 0
        WHERE id = :id";
        
        $update_flag_query = $this->db->prepare($update_flag);
        $update_flag_query->execute(array(':id' => $this->flag['id']));

        // Get all voters
        $get_all_votes = "SELECT *
        FROM vote
        WHERE flag = :flag";
        
        $votes = array();
        $get_all_votes_query = $this->db->prepare($get_all_votes);
        $get_all_votes_query->execute(array(':flag' => $this->flag['id']));
        while ($row = $get_all_votes_query->fetch(PDO::FETCH_ASSOC)) {
            $votes[] = $row;
        }

        // Loop all votes
        foreach ($votes as $v) {
            // Check what values to update
            $karma_value_string = '';
            if ($v['value'] == $type) {
                $karma_value_string = '+2';
            }
            else {
                $karma_value_string = '-2';
            }

            // Run the actual query
            $update_user_karma = "UPDATE user
            SET karma = karma" . $karma_value_string . "
            WHERE id = :id";
            
            $update_user_karma_query = $this->db->prepare($update_user_karma);
            $update_user_karma_query->execute(array(':id' => $v['user']));
        }
        
        // Update karma for the owner
        $karma_value_string = '';
        if ($type) {
            $karma_value_string = '+3';
        }
        else {
            $karma_value_string = '-3';
        }

        // Update owner karma
        $update_user_karma = "UPDATE user
        SET karma=karma" . $karma_value_string . "
        WHERE id = :id";
        
        $update_user_karma_query = $this->db->prepare($update_user_karma);
        $update_user_karma_query->execute(array(':id' => $this->flag['user']));

        // Create history
        $this->addHistory($this->flag['user'], $this->element->getId(), '%u slettet <b>' . $this->element->getName() . '</b>.');
    }

    //
    // Method for adding a history entry
    //

    private function addHistory($user, $file, $text) {
        $insert_history = "INSERT INTO history
        (user, file, history_text)
        VALUES (:user, :file, :text)";
        
        $insert_history_query = $this->db->prepare($insert_history);
        $insert_history_query->execute(array(':user' => $user, ':file' => $file, ':text' => $text));
    }

    //
    // Generic method for generating SEO friendly urls and directory names
    //

    private function generateUrlFriendly($s, $for_url = false) {
        $s = strtolower($s);
        $s = str_replace(array('Æ', 'Ø', 'Å'), array('ae', 'o', 'aa'), $s);
        $s = str_replace(array('æ', 'ø', 'å'), array('ae', 'o', 'aa'), $s);

        if ($for_url) {
            $s = str_replace(' ', '-', $s);
        }
        else {
            $s = str_replace(' ', '_', $s);
        }
        
        return $s;
    }
}
?>
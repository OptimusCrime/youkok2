<?php
/*
 * File: executioner.php
 * Holds: Executes completed flags
 * Created: 14.04.14
 * Last updated: 15.04.14
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
    }

    //
    // Execute - ???
    //

    private function execute1() {

    }
}
?>
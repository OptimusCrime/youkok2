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
        $row = $get_flag_query->fetch(PDO::FETCH_ASSOC);
        
        // Check if flag was returned
        if (!isset($row['id'])) {
            $response['code'] = 500;
        }
        else {
            if ($row['active'] == 1) {
                // Flag okey, check if reach 5 votes
                $get_votes1 = "SELECT COUNT(id) as 'votes'
                FROM vote 
                WHERE flag = :flag
                AND value = 1";
                
                $get_votes1_query = $this->db->prepare($get_votes1);
                $get_votes1_query->execute(array(':flag' => $this->flag));
                $votes1 = $get_votes1_query->fetch(PDO::FETCH_ASSOC);

                $get_votes2 = "SELECT COUNT(id) as 'votes'
                FROM vote 
                WHERE flag = :flag
                AND value = 0";
                
                $get_votes2_query = $this->db->prepare($get_votes2);
                $get_votes2_query->execute(array(':flag' => $this->flag));
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
                    if ($row['type'] == 0) {
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
        $update_flag_query->execute(array(':id' => $this->flag));

        // Update users
        // TODO
    }

    //
    // Execute - ???
    //

    private function execute1() {

    }
}
?>
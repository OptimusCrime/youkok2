<?php
/*
 * File: executioner.php
 * Holds: Executes completed flags
 * Created: 14.04.14
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

    private $controller;
    private $item;
    private $flag;

    //
    // Letters that we are going to need for later
    //

    private static $letters = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o',
                                    'p','q','r','s','t','u','v','w','x','y','z'); 

    //
    // Constructor
    //
    
    public function __construct($controller, $item, $flag) {
        // Pointers
        $this->controller = &$controller;
        $this->item = $item;
        $this->flag = $flag;

        // Let's go
        $this->analyze();             
    }

    //
    // Analyze what kind of flag to execute
    //

    private function analyze() {
        if ($this->flag->isActive()) {
            // Get votes
            $votes = $this->flag->getVotes();
            $votes_positive = 0;
            $votes_negative = 0;

            // Loop votes and sum the number of them
            foreach ($votes as $v) {
                if ($v->getValue()) {
                    $votes_positive++;
                }
                else {
                    $votes_negative++;
                }
            }

            // Check if finished
            $is_finished = false;
            $finished_type = null;
            if ($this->flag->getType() == 0) {
                if ($votes_positive == 2) {
                    $is_finished = true;
                    $finished_type = true;
                }
                else if ($votes_negative == 5) {
                    $is_finished = true;
                    $finished_type = false;
                }
            }
            else {
                if ($votes_positive == 5) {
                    $is_finished = true;
                    $finished_type = true;
                }
                else if ($votes_negative == 5) {
                    $is_finished = true;
                    $finished_type = false;
                }
            }

            // Check if we should execute anything
            if ($is_finished) {
                // Executeeeee
                if ($this->flag->getType() == 0) {
                    $this->execute0($finished_type);
                }
                else if ($this->flag->getType() == 1) {
                    $this->execute1($finished_type);
                }
                else if ($this->flag->getType() == 2) {
                    $this->execute2($finished_type);
                }
                
                // Refresh cache
                $this->controller->cacheManager->deleteCache($this->flag->getFile(), 'i');
            }
        }
    }

    //
    // Execute - Godkjenn
    //

    private function execute0($type) {
        // Update element
        if ($type) {
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
        
        $update_element_query = $this->controller->db->prepare($update_element);
        $update_element_query->execute(array(':id' => $this->item->getId()));

        // Update flag
        $update_flag = "UPDATE flag
        SET active = 0
        WHERE id = :id";
        
        $update_flag_query = $this->controller->db->prepare($update_flag);
        $update_flag_query->execute(array(':id' => $this->flag->getId()));

        // Defining the karma values
        $karma_values = array(
            'voter' => array(
                'file_pending' => 1,
                'file_wrong' => 1,
                'file_correct' => 1,

                'directory_pending' => 1,
                'directory_wrong' => 1,
                'directory_correct' => 1,
            ),
            'owner' => array(
                'file_pending' => 3,
                'file_wrong' => 1,
                'file_correct' => 3,

                'directory_pending' => 1,
                'directory_wrong' => 1,
                'directory_correct' => 1,
            ),
        );

        // Update karma for all voters
        $this->updateKarma($karma_values, $type, false);

        // Create history
        $this->addHistory($this->flag->getUser(), 
                          $this->item->getId(),
                          $this->item->getName() . ' av %u ble godkjent.');
    }

    //
    // Execute - Navn
    //

    private function execute1($type) {
        if ($type) {
            $flag_data = $this->flag->getData();

            // Check duplicates for url friendly
            $url_friendly = $this->controller->utils->generateUrlFriendly($flag_data['name'], true);
            $num = 2;
            
            while (true) {
                $get_duplicate = "SELECT id
                FROM archive 
                WHERE parent = :id
                AND url_friendly = :url_friendly";
                
                $get_duplicate_query = $this->controller->db->prepare($get_duplicate);
                $get_duplicate_query->execute(array(':id' => $this->item->getId(), 
                                                    ':url_friendly' => $url_friendly));
                $row_duplicate = $get_duplicate_query->fetch(PDO::FETCH_ASSOC);
                
                if (isset($row_duplicate['id'])) {
                    $url_friendly = $this->controller->utils->generateUrlFriendly($this->letters[rand(0, count($this->letters) - 1)] . 
                                                               $url_friendly);
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

            $update_element_query = $this->controller->db->prepare($update_element);
            $update_element_query->execute(array(':id' => $this->item->getId(),
                                                 ':name' => $flag_data['name'],
                                                 ':url_friendly' => $url_friendly));
        }

        // Update flag
        $update_flag = "UPDATE flag
        SET active = 0
        WHERE id = :id";
        
        $update_flag_query = $this->controller->db->prepare($update_flag);
        $update_flag_query->execute(array(':id' => $this->flag->getId()));

        // Defining the karma values
        $karma_values = array(
            'voter' => array(
                'pending' => 1,
                'wrong' => 1,
                'correct' => 1,
            ),
            'owner' => array(
                'pending' => 3,
                'wrong' => 2,
                'correct' => 3,
            ),
        );

        // Update karma for all voters
        $this->updateKarma($karma_values, $type, true);

        // Create history
        $this->addHistory($this->flag->getUser(), 
                          $this->item->getId(), 
                          '%u endret navn fra <b>' . $this->item->getName() . '</b> til <b>' . $flag_data['name'] . '</b>.');
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
        $this->addHistory($this->flag['user'],
                          $this->element->getId(), 
                          '%u slettet <b>' . $this->element->getName() . '</b>.');
    }

    //
    // Update karma values
    //

    private function updateKarma($values, $type, $ignore_file_dir) {
        // Check what field to read
        if ($ignore_file_dir) {
            $values_prefix = '';
        }
        else {
            if ($this->item->isDirectory()) {
                $values_prefix = 'directory_';
            }
            else {
                $values_prefix = 'file_';
            }
        }

        // Get all voters
        $votes = $this->flag->getVotes();

        // Loop all votes
        foreach ($votes as $v) {
            // Check what values to update
            if ($v->getValue() == $type) {
                $karma_value_value = $values['voter'][$values_prefix . 'correct'];
                $karma_value_prefix = '+';
                $karma_is_positive = true;
            }
            else {
                $karma_value_value = $values['voter'][$values_prefix . 'wrong'];
                $karma_value_prefix = '-';
                $karma_is_positive = false;
            }

            $karma_pending_value = $values['voter'][$values_prefix . 'pending'];

            // Run the actual query
            $update_user_karma = "UPDATE user
            SET karma=karma" . $karma_value_prefix . $karma_value_value . ",
                karma_pending=karma_pending-" . $karma_pending_value . "
            WHERE id = :id";
            
            $update_user_karma_query = $this->controller->db->prepare($update_user_karma);
            $update_user_karma_query->execute(array(':id' => $v->getUser()));

            // Update pending karma
            $update_user_pending_karma = "UPDATE history
            SET active = 0,
                positive = :positive,
                karma = :karma
            WHERE user = :user
            AND flag = :flag
            AND history_text IS NULL";
            
            $update_user_pending_karma_query = $this->controller->db->prepare($update_user_pending_karma);
            $update_user_pending_karma_query->execute(array(':positive' => $karma_is_positive,
                                                            ':karma' => $karma_value_value,
                                                            ':user' => $v->getUser(),
                                                            ':flag' => $this->flag->getId()));
        }
        
        // Update karma for the owner
        if ($type) {
            $karma_value_value = $values['owner'][$values_prefix . 'correct'];
            $karma_value_prefix = '+';
            $karma_is_positive = true;
        }
        else {
            $karma_value_value = $values['owner'][$values_prefix . 'wrong'];
            $karma_value_prefix = '+';
            $karma_is_positive = true;
        }

        $karma_pending_value = $values['owner'][$values_prefix . 'pending'];

        $update_user_karma = "UPDATE user
        SET karma=karma" . $karma_value_prefix . $karma_value_value . ",
            karma_pending=karma_pending-" . $karma_pending_value . "
        WHERE id = :id";
        
        $update_user_karma_query = $this->controller->db->prepare($update_user_karma);
        $update_user_karma_query->execute(array(':id' => $this->flag->getUser()));

        // Update pending karma
        $update_user_pending_karma = "UPDATE history
        SET active = 0,
            positive = :positive,
            karma = :karma
        WHERE user = :user
        AND flag = :flag
        AND history_text IS NULL";
        
        $update_user_pending_karma_query = $this->controller->db->prepare($update_user_pending_karma);
        $update_user_pending_karma_query->execute(array(':positive' => $karma_is_positive,
                                                        ':karma' => $karma_value_value,
                                                        ':user' => $this->flag->getUser(),
                                                        ':flag' => $this->flag->getId()));
    }

    //
    // Method for adding a history entry
    //

    private function addHistory($user, $file, $text) {
        $insert_history = "INSERT INTO history
        (user, file, history_text)
        VALUES (:user, :file, :text)";
        
        $insert_history_query = $this->controller->db->prepare($insert_history);
        $insert_history_query->execute(array(':user' => $user, 
                                             ':file' => $file, 
                                             ':text' => $text));
    }
}
?>
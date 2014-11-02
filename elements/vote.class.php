<?php
/*
 * File: vote.php
 * Holds: Class for a vote belonging to a flag
 * Created: 11.05.14
 * Project: Youkok2
 * 
*/

//
// Holds a vote for a flag
//

class Vote {

    //
    // Variables
    //

    // Pointer to the controller
    private $controller;

    // Database fields
    private $id;
    private $user;
    private $flag;
    private $value;
    private $voted;

    public function __construct($controller) {
        // Store references
        $this->controller = &$controller;

        // Set all fields to null first
        $this->id = null;
        $this->user = null;
        $this->value = null;
        $this->voted = null;
    }

    //
    // Setters
    //

    public function setAll($arr) {
        $this->id = $arr['id'];
        $this->user = $arr['user'];
        $this->value = $arr['value'];
        $this->voted = $arr['voted'];
    }

    //
    // Getters
    //

    public function getId() {
        return $this->id;
    }

    public function getUser() {
        return $this->user;
    }

    public function getFlag() {
        return $this->flag;
    }

    public function getValue() {
        return $this->value;
    }

    public function getVoted() {
        return $this->voted;
    }

    //
    // Setters
    //

    public function setId($id) {
        $this->id = $id;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function setFlag($flag) {
        $this->flag = $flag;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function setVoted($voted) {
        $this->voted = $voted;
    }

    //
    // New
    //

    public function createNew() {
        // Insert etc here
        $create_vote = "INSERT INTO vote
        (user, flag, value)
        VALUES (:user, :flag, :value)";

        $create_vote_query = $this->controller->db->prepare($create_vote);
        $create_vote_query->execute(array(':user' => $this->user, 
                                          ':flag' => $this->flag, 
                                          ':value' => $this->value));
    }

    //
    // Update
    //

    public function updateVote() {
        $update_vote = "UPDATE vote
        SET value = :value, voted = NOW()
        WHERE id = :id";

        $update_vote_query = $this->controller->db->prepare($update_vote);
        $update_vote_query->execute(array(':value' => $this->value, 
                                          ':id' => $this->id));
    }
}
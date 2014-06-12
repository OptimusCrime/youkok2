<?php
/*
 * File: course.class.php
 * Holds: Class for a course
 * Created: 30.05.14
 * Project: Youkok2
 * 
*/

//
// Holds a Course
//

class Course {
    
    //
    // Some variables
    //
    
    private $db;

    private $id;
    private $code;
    private $name;
    
    //
    // Constructor
    //
    
    public function __construct($db) {
    	// Store db reference
    	$this->db = $db;

    	// Set all fields to null first
    	$this->id = null;
    	$this->code = null;
    	$this->name = null;
    }

    //
    // Setters
    //

    public function setId($id) {
    	$this->id = $id;
    }
    public function setCode($code) {
    	$this->code = $code;
    }
    public function setName($name) {
    	$this->name = $name;
    }

    //
    // Fetch
    //

    private function fetch($args) {
    	$get_course = "SELECT " . implode(',', $args) . "
        FROM course
        WHERE id = :id";
        
        $get_course_query = $this->db->prepare($get_course);
        $get_course_query->execute(array(':id' => $this->id));
        $row = $get_course_query->fetch(PDO::FETCH_ASSOC);

        foreach ($args as $v) {
        	if (isset($row[$v])) {
        		$this->$v = $row[$v];
        	}
        }
    }

    //
    // Get
    //
    
    public function getId() {
    	return $this->id;
    }
    public function getCode() {
    	if ($this->code == null) {
    		$this->fetch(array('code'));
    	}
    	return $this->code;
    }
    public function getName() {
    	if ($this->name == null) {
    		$this->fetch(array('name'));
    	}
    	return $this->name;
    }
}
?>
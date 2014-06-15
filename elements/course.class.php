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
    
    private $controller;

    private $id;
    private $code;
    private $name;
    
    //
    // Constructor
    //
    
    public function __construct($controller) {
    	// Store db reference
    	$this->controller = &$controller;

    	// Set all fields to null first
    	$this->id = null;
    	$this->code = null;
    	$this->name = null;
    }

    //
    // Setters
    //

    public function setId($id) {
        // Store id
    	$this->id = $id;

        // Check if cached
        if ($this->controller->cacheManager->isCached($id, 'c')) {
            // Get the cached array
            $temp_cache_data = $this->controller->cacheManager->getCache($id, 'c');

            // Fields to look up
            $fields = array('name', 'code');
            
            // Loop all the fields and apply data
            foreach ($temp_cache_data as $k => $v) {
                $this->$k = $v;
            }
        }
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
        
        $get_course_query = $this->controller->db->prepare($get_course);
        $get_course_query->execute(array(':id' => $this->id));
        $row = $get_course_query->fetch(PDO::FETCH_ASSOC);

        foreach ($args as $v) {
        	if (isset($row[$v])) {
        		$this->$v = $row[$v];
        	}
        }

        // Cache the new result
        $this->controller->cacheManager->setCache($this->id, 'c', $this->cacheFormat());
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

    //
    // Create cache string for this Course
    //

    private function cacheFormat() {
        $cache_temp = array();
        $fields = array('name', 'code');
        
        // Loop each field
        foreach ($fields as $v) {
            if ($this->$v != null) {
                $cache_temp[] = "'" . $v . "' => '" . addslashes($this->$v) . "'";
            }
        }

        // Implode and return
        return implode(', ', $cache_temp);
    }
}
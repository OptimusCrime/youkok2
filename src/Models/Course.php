<?php
/*
 * File: Course.php
 * Holds: Holds a Course
 * Created: 11.11.2014
 * Project: Youkok2
*/

namespace Youkok2\models;

/*
 * Loads other classes
 */

use \Youkok2\Models\Controllers\CourseController as CourseController;

class Course {
    
    /*
     * Variables
     */
    
    private $id;
    private $code;
    private $name;
    
    /*
     * Constructor
     */
    
    public function __construct() {
        $this->controller = new CourseController($this);
    }
    
    /*
     * Getters
     */

    public function getId() {
        return $this->id;
    }
    public function getCode() {
        return $this->code;
    }
    public function getName() {
        return $this->name;
    }
    
    /*
     * Setters
     */
    
    public function setId($id) {
        $this->id = $id;
    }
    public function setCode($code) {
        $this->code = $code;
    }
    public function setName($name) {
        $this->name = $name;
    }
    
    /*
     * Redirectors
     */
    
    public function createById($id) {
        $this->controller->createById($id);
    }
    public function save() {
        $this->controller->save();
    }
    public function update() {
        $this->controller->update();
    }
} 
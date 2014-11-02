<?php
namespace Youkok2\models;

class Element {

    /*
     * Variables
     */

    public $controller;

    private $id;
    private $name;
    private $urlFriendly;
    private $parent;
    private $course;
    private $location;
    private $mimeType;
    private $missingImage;
    private $size;
    private $isDirectory;
    private $isAccepted;
    private $isVisibile;
    private $url;
    private $added;
    
    /*
     * Constructor
     */
    
    public function __construct() {
        $this->controller = new ElementController($this);
    }
    
    /*
     * Getters
     */

    public function getId() {
        return $this->id;
    }
    public function getName() {
        return $this->name;
    }
    public function getUrlFriendly() {
        return $this->$urlFriendly;
    }
    public function getParent() {
        return $this->parent;
    }
    public function getCourse() {
        return $this->course;
    }
    public function getLocation() {
        return $this->location;
    }

    /*
     * Setters
     */

    public function setId($id) {
        $this->id = $id;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function setUrlFriendly($url) {
        $this->$urlFriendly = $url;
    }
    public function setParent($parent) {
        $this->parent = $parent;
    }
    public function setCourse($course) {
        $this->course = $course;
    }
    public function setLocation($location) {
        $this->location = $location;
    }
    
    /*
     * Reoute special calls
     */
    
    public function __call($method, $args) {
        echo $method;
    }
} 
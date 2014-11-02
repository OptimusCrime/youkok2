<?php
namespace Youkok2\models;

class Element {

    /*
     * Variables
     */

    public $controller;

    protected $id;
    protected $name;
    protected $urlFriendly;
    protected $parent;
    protected $course;
    protected $location;

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
} 
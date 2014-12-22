<?php
/*
 * File: Element.php
 * Holds: Holds a Element
 * Created: 11.11.2014
 * Project: Youkok2
*/

namespace Youkok2\Models;

/*
 * Load different classes into namespace
 */

use \Youkok2\Models\Controllers\ElementController as ElementController;

/*
 * Model for a Element
 */

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
    private $directory;
    private $accepted;
    private $visibile;
    private $url;
    private $added;
    
    /*
     * Constructor
     */
    
    public function __construct() {
        $this->controller = new ElementController($this);

        /*
         * Set some default values
         */

        $this->parent = null;
        $this->course = null;
        $this->location = ''; // Backwards compability
        $this->mimeType = null;
        $this->missingImage = 0;
        $this->size = null;
        $this->directory = false;
        $this->accepted = false;
        $this->visible = true;
        $this->url = null;

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
        return $this->urlFriendly;
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
    public function getMimeType() {
        return $this->mimeType;
    }
    public function getMissingImage() {
        return (bool) $this->missingImage;
    }
    public function getSize() {
        return $this->size;
    }
    public function isDirectory() {
        return (bool) $this->directory;
    }
    public function isLink() {
        return ($this->url != null);
    }
    public function isFile() {
        return ($this->url == null and !$this->directory);
    }
    public function isAccepted() {
        return (bool) $this->accepted;
    }
    public function isVisible() {
        return (bool) $this->visibile;
    }
    public function getUrl() {
        return $this->url;
    }
    public function getAdded() {
        return $this->added;
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
        $this->urlFriendly = $url;
    }
    public function setParent($parent) {
        $this->parent = $parent;
    }
    public function setCourse($course) {
        if (is_object($course)) {
            $this->course = &$course;

        }
        else {
            $this->controller->setCourseFromId($course);
        }
    }
    public function setLocation($location) {
        $this->location = $location;
    }
    public function setMimeType($mime) {
        $this->mimeType = $mime;
    }
    public function setMissingImage($missingImage) {
        $this->missingImage = $missingImage;
    }
    public function setSize($size) {
        $this->size = $size;
    }
    public function setDirectory($directory) {
        $this->directory = $directory;
    }
    public function setAccepted($accepted) {
        $this->accepted = $accepted;
    }
    public function setVisible($visible) {
        $this->visibile = $visible;
    }
    public function setUrl($url) {
        $this->url = $url;
    }
    public function setAdded($added) {
        $this->added = $added;
    }
    
    /*
     * Redirectors
     */
    
    public function createById($id) {
        $this->controller->createById($id);
    }
    public function createByUrl($url) {
        $this->controller->createByUrl($url);
    }
    public function save() {
        $this->controller->save();
    }
    public function update() {
        $this->controller->update();
    }
} 
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

use \Youkok2\Models\BaseModel as BaseModel;
use \Youkok2\Models\Controllers\ElementController as ElementController;
use \Youkok2\Models\StaticControllers\ElementStaticController as ElementStaticController;

/*
 * Model for a Element
 */

class Element extends BaseModel {

    /*
     * Variables
     */

    public $controller;

    private $id;
    private $name;
    private $urlFriendly;
    private $owner;
    private $parent;
    private $empty;
    private $checksum;
    private $mimeType;
    private $missingImage;
    private $size;
    private $directory;
    private $accepted;
    private $visible;
    private $exam;
    private $url;
    private $added;
    
    /*
     * Schema
     */
    
    private $schema = [
        'id' => [
            'type' => 'integer',
            'null' => false,
        ],
        'name' => [
            'type' => 'string',
            'null' => false,
        ],
        'url_friendly' => [
            'method' => 'urlFriendly',
            'type' => 'string',
            'null' => false,
        ],
        'owner' => [
            'type' => 'integer',
            'null' => true,
            'default' => null,
        ],
        'parent' => [
            'type' => 'integer',
            'null' => true,
            'default' => null,
        ],
        'empty' => [
            'type' => 'integer',
            'null' => false,
            'default' => 1,
        ],
        'checksum' => [
            'type' => 'string',
            'null' => true,
            'default' => null,
        ],
        'mime_type' => [
            'method' => 'mimeType',
            'type' => 'string',
            'null' => true,
            'default' => null,
        ],
        'missing_image' => [
            'method' => 'missingImage',
            'type' => 'integer',
            'null' => false,
            'default' => 0,
        ],
        'size' => [
            'type' => 'integer',
            'null' => true,
            'default' => null,
        ],
        'is_directory' => [
            'method' => 'directory',
            'type' => 'integer',
            'null' => false,
            'default' => 0,
        ],
        'is_accepted' => [
            'method' => 'accepted',
            'type' => 'integer',
            'null' => false,
            'default' => 0,
        ],
        'is_visible' => [
            'method' => 'visible',
            'type' => 'integer',
            'null' => false,
            'default' => 1,
        ],
        'exam' => [
            'type' => 'datetime',
            'null' => true,
            'default' => null,
        ],
        'url' => [
            'type' => 'string',
            'null' => true,
            'default' => null,
        ],
        'added' => [
            'type' => 'datetime',
            'null' => false,
        ],
    ];
    
    /*
     * Constructor
     */
    
    public function __construct() {
        $this->controller = new ElementController($this);
        
        /*
         * Set some default values
         */
        
        $this->setDefaults($this, $this->schema);
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
    public function getOwner() {
        return $this->owner;
    }
    public function getParent() {
        return $this->parent;
    }
    public function isEmpty() {
        return (bool) $this->empty;
    }
    public function getChecksum() {
        return $this->checksum;
    }
    public function getMimeType() {
        return $this->mimeType;
    }
    public function getMissingImage() {
        return (bool) $this->missingImage;
    }
    public function getSize($pretty = false) {
        return $pretty ? Utilities::prettifyFilesize($this->size) : $this->size;
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
        return (bool) $this->visible;
    }
    public function getExam() {
        return $this->exam;
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
    public function setOwner($owner) {
        $this->owner = $owner;
    }
    public function setParent($parent) {
        $this->parent = $parent;
    }
    public function setEmpty($empty) {
        $this->empty = (bool) $empty;
    }
    public function setChecksum($checksum) {
        $this->checksum = $checksum;
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
        $this->directory = (bool) $directory;
    }
    public function setAccepted($accepted) {
        $this->accepted = (bool) $accepted;
    }
    public function setVisible($visible) {
        $this->visible = (bool) $visible;
    }
    public function setExam($exam) {
        $this->exam = $exam;
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
    
    public function createById($id, $skip_db = false) {
        $this->controller->createById($id, $skip_db);
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
    
     /*
     * Static functions overload
     */
    
    public static function __callStatic($name, $arguments) {
        // Check if method exists
        if (method_exists('\Youkok2\Models\StaticControllers\ElementStaticController', $name)) {
            // Call method and return response
            return call_user_func_array(array('\Youkok2\Models\StaticControllers\ElementStaticController', 
                $name), $arguments);
        }
    }
} 
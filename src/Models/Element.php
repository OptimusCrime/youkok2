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
use \Youkok2\Models\StaticControllers\ElementStaticController as ElementStaticController;
use \Youkok2\Utilities\Utilities as Utilities;

/*
 * Model for a Element
 */

class Element extends BaseModel {

    /*
     * Variables
     */

    private $controller;

    // Fields in the database
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
        'meta' => [
            'table' => 'archive',
        ],
        'fields' => [
            'id' => [
                'type' => 'integer',
                'null' => false,
                'db' => true,
            ],
            'name' => [
                'type' => 'string',
                'null' => false,
                'db' => true,
            ],
            'url_friendly' => [
                'method' => 'urlFriendly',
                'type' => 'string',
                'null' => false,
                'db' => true,
            ],
            'owner' => [
                'type' => 'integer',
                'null' => true,
                'default' => null,
                'db' => true,
            ],
            'parent' => [
                'type' => 'integer',
                'null' => true,
                'default' => null,
                'db' => true,
            ],
            'empty' => [
                'type' => 'integer',
                'null' => false,
                'default' => 1,
                'db' => true,
            ],
            'checksum' => [
                'type' => 'string',
                'null' => true,
                'default' => null,
                'db' => true,
            ],
            'mime_type' => [
                'method' => 'mimeType',
                'type' => 'string',
                'null' => true,
                'default' => null,
                'db' => true,
            ],
            'missing_image' => [
                'method' => 'missingImage',
                'type' => 'integer',
                'null' => false,
                'default' => 0,
                'db' => true,
            ],
            'size' => [
                'type' => 'integer',
                'null' => true,
                'default' => null,
                'db' => true,
            ],
            'is_directory' => [
                'method' => 'directory',
                'type' => 'integer',
                'null' => false,
                'default' => 0,
                'db' => true,
                'is' => true
            ],
            'is_accepted' => [
                'method' => 'accepted',
                'type' => 'integer',
                'null' => false,
                'default' => 0,
                'db' => true,
                'is' => true
            ],
            'is_visible' => [
                'method' => 'visible',
                'type' => 'integer',
                'null' => false,
                'default' => 1,
                'db' => true,
                'is' => true
            ],
            'exam' => [
                'type' => 'datetime',
                'null' => true,
                'default' => null,
                'db' => true,
            ],
            'url' => [
                'type' => 'string',
                'null' => true,
                'default' => null,
                'db' => true,
            ],
            'added' => [
                'type' => 'datetime',
                'null' => false,
                'db' => true,
            ],
        ]
    ];
    
    /*
     * Constructor
     */
    
    public function __construct($data) {
        $this->controller = new ElementController($this);
        
        /*
         * Set some default values
         */
        
        $this->setDefaults($this);

        /*
         * Create
         */

        if (is_numeric($data)) {
            $this->controller->createById($data);
        }
        else {
            $this->controller->createByUrl($data);
        }
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
    public function getParent($object = false) {
        if ($object === false) {
            return $this->parent;
        }
        else {
            return $this->controller->getParentObject();
        }
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
    public function getAdded($pretty = false) {
        return $pretty ? Utilities::prettifySQLDate($this->added) : $this->added;
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
     * Return schema
     */

    public function getSchema() {
        return $this->schema;
    }

    /*
     * Functions overload
     */

    public function __call($name, $arguments) {
        // Check if method exists
        if (method_exists('\Youkok2\Models\Controllers\ElementController', $name)) {
            // Call method and return response
            return call_user_func_array(array($this->controller,
                $name), $arguments);
        }
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
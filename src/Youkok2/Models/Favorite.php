<?php
/*
 * File: Favorite.php
 * Holds: Holds a favorite entry
 * Created: 20.08.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Models;

use Youkok2\Models\Controllers\FavoriteController;

class Favorite extends BaseModel {

    /*
     * Variables
     */

    protected $controller;

    // Fields in the database
    private $id;
    private $file;
    private $user;
    private $favoritedTime;

    /*
     * Schema
     */

    protected $schema = [
        'meta' => [
            'table' => 'favorite',
            'cacheable' => false,
        ],
        'fields' => [
            // Database fields
            'id' => [
                'type' => 'integer',
                'null' => false,
                'db' => true,
                'arr' => true,
                'ignore_insert' => true,
            ],
            'file' => [
                'type' => 'integer',
                'null' => false,
                'db' => true,
                'arr' => true,
            ],
            'user' => [
                'type' => 'integer',
                'null' => false,
                'db' => true,
                'arr' => true,
            ],
            'favorited_time' => [
                'method' => 'favoritedTime',
                'type' => 'integer',
                'default' => 'NOW()',
                'null' => false,
                'db' => true,
                'arr' => true,
            ],
        ]
    ];

    /*
     * Constructor
     */

    public function __construct($data = null) {
        $this->controller = new FavoriteController($this);

        /*
         * Set some default values
         */

        $this->setDefaults();

        /*
         * Various create methods are called here
         */

        if (is_numeric($data)) {
            $this->controller->createById($data);
        }
        elseif (is_array($data)) {
            $this->controller->createByArray($data);
        }
    }

    /*
     * Getters
     */

    public function getId() {
        return $this->id;
    }
    public function getFile() {
        return $this->file;
    }
    public function getUser() {
        return $this->user;
    }
    public function getFavoritedTime() {
        return $this->favoritedTime;
    }

    /*
     * Setters
     */

    public function setId($id) {
        $this->id = $id;
    }
    public function setFile($file) {
        $this->file = $file;
    }
    public function setUser($user) {
        $this->user = $user;
    }
    public function setFavoritedTime($time) {
        $this->favoritedTime = $time;
    }
} 
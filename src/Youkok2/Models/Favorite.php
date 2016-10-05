<?php
namespace Youkok2\Models;

use Youkok2\Models\Controllers\BaseController;

class Favorite extends BaseModel
{

    protected $controller;
    
    private $id;
    private $file;
    private $user;
    private $favoritedTime;

    protected $schema = [
        'meta' => [
            'table' => 'favorite',
            'cacheable' => false,
        ],
        'fields' => [
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
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false,
                'db' => true,
                'arr' => true,
            ],
        ]
    ];

    public function __construct($data = null) {
        $this->controller = new BaseController(null, $this);

        $this->setDefaults();

        if (is_numeric($data)) {
            $this->controller->createById($data);
        }
        elseif (is_array($data)) {
            $this->controller->createByArray($data);
        }
    }

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

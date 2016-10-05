<?php
namespace Youkok2\Models;

use Youkok2\Models\Controllers\BaseController;

class Download extends BaseModel
{

    protected $controller;

    private $id;
    private $file;
    private $downloadedTime;
    private $ip;
    private $agent;
    private $user;

    protected $schema = [
        'meta' => [
            'table' => 'download',
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
            'downloaded_time' => [
                'type' => 'integer',
                'method' => 'downloadedTime',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
                'db' => true,
                'arr' => true,
            ],
            'ip' => [
                'type' => 'string',
                'null' => false,
                'db' => true,
                'arr' => true,
            ],
            'agent' => [
                'type' => 'string',
                'null' => true,
                'default' => null,
                'db' => true,
                'arr' => true,
            ],
            'user' => [
                'type' => 'integer',
                'null' => true,
                'default' => null,
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
    public function getDownloadedTime() {
        return $this->downloadedTime;
    }
    public function getIp() {
        return $this->ip;
    }
    public function getAgent() {
        return $this->agent;
    }
    public function getUser() {
        return $this->user;
    }

    public function setId($id) {
        $this->id = $id;
    }
    public function setFile($file) {
        $this->file = $file;
    }
    public function setDownloadedTime($time) {
        $this->downloadedTime = $time;
    }
    public function setIp($ip) {
        $this->ip = $ip;
    }
    public function setAgent($agent) {
        $this->agent = $agent;
    }
    public function setUser($user) {
        $this->user = $user;
    }
}

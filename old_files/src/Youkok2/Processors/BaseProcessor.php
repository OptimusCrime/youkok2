<?php
namespace Youkok2\Processors;

use Youkok2\Models\Me;
use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Database;
use Youkok2\Views\Processors;

abstract class BaseProcessor extends Processors
{
    
    private $method;
    private $data;
    
    public function __construct($app) {
        parent::__construct($app);
        
        $this->data = [];
    }

    public function setMethod($method) {
        if ($method === null) {
            $this->method = 'run';
        }
        else {
            $this->method = $method;
        }
    }
    
    public function execute() {
        parent::run();

        if ($this->requireDatabase()) {
            $this->makeDatabaseConnection();
        }
        
        if ($this->canBeLoggedIn()) {
            $this->me = new Me($this->application);
        }

        if (!$this->checkPermissions()) {
            $this->setData('code', 500);
            $this->setData('msg', 'No access');
        }

        $this->{$this->method}();

        $this->handleOutput();
    }

    protected function checkPermissions() {
        return true;
    }

    protected function requireCli() {
        return php_sapi_name() == 'cli';
    }
    protected function requireAdmin() {
        if (Database::$db === null) {
            if (!$this->makeDatabaseConnection()) {
                return false;
            }
        }

        $this->me = new Me($this->application);

        return $this->me->isAdmin();
    }
    protected function requireLoggedIn() {
        if (Database::$db === null) {
            if (!$this->makeDatabaseConnection()) {
                return false;
            }
        }

        $this->me = new Me($this->application);

        return $this->me->isLoggedIn();
    }
    protected function requireForce() {
        return $this->getSetting('force');
    }

    protected function requireDatabase() {
        return false;
    }

    protected function canBeLoggedIn() {
        return false;
    }

    protected function encodeData($data) {
        return $data;
    }
    
    protected function setData($key, $data) {
        $this->data[$key] = $data;
    }
    protected function setAllData($data) {
        $this->data = $data;
    }
    public function getData() {
        $return_data = $this->data;

        if ($this->getSetting('encode') === null or $this->getSetting('encode')) {
            $return_data = $this->encodeData($return_data);
        }

        return $return_data;
    }

    protected function handleOutput() {
        // About to output, make sure cachemanager is storing everything
        CacheManager::store();
        
        if ($this->getSetting('close_db') and $this->getSetting('close_db')) {
            $this->closeConnection();
        }
        
        $output_data = $this->data;
        if ($this->getSetting('encode') === null or $this->getSetting('encode')) {
            $output_data = $this->encodeData($output_data);
        }
        
        if ($this->getSetting('application')) {
            $this->application->setBody(json_encode($output_data));
        }
        else {
            if (php_sapi_name() == 'cli' && $this->getSetting('output') !== false) {
                $climate = new \League\CLImate\CLImate;
                $climate->json($output_data);
            }
        }
        
        return $output_data;
    }
    
    protected function setError() {
        $this->setData('code', 500);
        $this->setData('msg', 'Something went wrong');
    }

    protected function setOk() {
        $this->setData('code', 200);
        $this->setData('msg', 'OK');
    }

    private function makeDatabaseConnection() {
        if (Database::$db !== null) {
            return true;
        }
        
        try {
            Database::connect();
            return true;
        }
        catch (Exception $e) {
            $this->setData('code', 500);
            $this->setData('msg', 'Could not connect to database');

            return false;
        }
    }
    
    private function closeConnection() {
        if (Database::$db !== null) {
            Database::close();
        }
    }
}

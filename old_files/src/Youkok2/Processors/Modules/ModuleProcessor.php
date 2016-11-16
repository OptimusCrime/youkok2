<?php
namespace Youkok2\Processors\Modules;

use Youkok2\Processors\BaseProcessor;

abstract class ModuleProcessor extends BaseProcessor
{

    protected function checkPermissions() {
        return $this->requireLoggedIn();
    }

    protected function requireDatabase() {
        return true;
    }
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function get() {
        parent::run();
    }
    
    public function update() {
        parent::run();
    }
}

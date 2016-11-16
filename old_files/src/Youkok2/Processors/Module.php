<?php
namespace Youkok2\Processors;

use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Models\Controllers\ElementController;
use Youkok2\Utilities\ClassParser;
use Youkok2\Utilities\Database;

class Module extends BaseProcessor
{
    
    private static $modules = [
        'Processors\Modules\MostPopularElements' => 1,
        'Processors\Modules\MostPopularCourses' => 2,
    ];
    
    private $module = null;

    protected function canBeLoggedIn() {
        return true;
    }
    
    protected function requireDatabase() {
        return true;
    }

    protected function encodeData($data) {
        $new_data = [];

        if (isset($data['data']) and count($data['data']) > 0) {
            foreach ($data['data'] as $v) {
                $new_data[] = $v->toArray();
            }
        }

        $data['data'] = $new_data;

        return $data;
    }
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    private function getModule($settings) {
        if (!isset($settings['module'])) {
            return null;
        }
        
        foreach (self::$modules as $k => $v) {
            if ($v == $settings['module']) {
                $this->module = $k;
                break;
            }
        }
    }

    private function getModuleSettings() {
        $settings = $this->getSettings();
        $settings['application'] = false;
        $settings['close_db'] = false;
        if (isset($_POST['module'])) {
            $settings['module'] = $_POST['module'];
        }
        if (isset($_POST['module1_delta'])) {
            $settings['module1_delta'] = $_POST['module1_delta'];
        }
        if (isset($_POST['module2_delta'])) {
            $settings['module2_delta'] = $_POST['module2_delta'];
        }

        return $settings;
    }
    
    public function get() {
        $settings = $this->getModuleSettings();

        $this->getModule($settings);
        
        if ($this->module != null) {
            $module_instance = $this->application->runProcessor(
                new ClassParser($this->module, 'get'),
                $settings
            );
            
            $this->setData('data', $module_instance->getData()['data']);
            
            $this->setOK();
        }
        else {
            $this->setError();
        }
    }
    
    public function update() {
        $settings = $this->getModuleSettings();
        
        $this->getModule($settings);
        
        if ($this->module != null) {
            $module_instance = $this->application->runProcessor(
                new ClassParser($this->module, 'update'),
                $settings
            );
            
            $this->setData('data', $module_instance->getData()['data']);

            $this->setOK();
        }
        else {
            $this->setError();
        }
    }
}

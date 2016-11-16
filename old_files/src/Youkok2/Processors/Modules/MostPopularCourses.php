<?php
namespace Youkok2\Processors\Modules;

use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Models\Cache\CourseDownloads;
use Youkok2\Models\Controllers\CourseDownloadsController;
use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Database;

class MostPopularCourses extends ModuleProcessor
{

    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function get() {
        if ($this->getSetting('module2_delta') !== null and !is_array($this->getSetting('module2_delta'))) {
            $delta_numeric = $this->getSetting('module2_delta');
        }
        else {
            $delta_numeric = $this->me->getModuleSettings('module2_delta');
        }
        
        if ($delta_numeric == null or $delta_numeric < 0 or $delta_numeric > 4) {
            $delta_numeric = 3;
        }
        
        $course_downloads = CacheManager::getCache($delta_numeric, 'cd');
        
        $collection = [];
        if (isset($course_downloads['data']) and strlen($course_downloads['data']) > 0) {
            $course_downloads_clean = json_decode($course_downloads['data'], true);

            if (is_array($course_downloads_clean) and count($course_downloads_clean) > 0) {
                foreach ($course_downloads_clean as $v) {
                    $element = Element::get($v['id']);
                    $element->setDownloadCount($delta_numeric, $v['downloaded']);
                    $collection[] = $element;
                }
            }
        }
        
        $this->setData('data', $collection);
    }
    
    public function update() {
        $delta_numeric = $this->getSetting('module2_delta');
        
        if ($delta_numeric < 0 or $delta_numeric > 4) {
            $delta_numeric = 3;
        }
        
        $this->me->setModuleSettings('module2_delta', $delta_numeric);
        
        if ($this->me->isLoggedIn()) {
            $this->me->update();
        }
        
        $this->get();
    }
}

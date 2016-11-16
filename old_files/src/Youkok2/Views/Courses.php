<?php
namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Loader;

class Courses extends BaseView
{
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        $this->template->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);

        $this->template->assign('HEADER_MENU', 'ARCHIVE');
        
        if (!$this->template->isCached('courses.tpl', $this->path)) {
            $this->loadCourses();
        }

        $this->displayAndCleanup('courses.tpl', $this->path);
    }

    private function loadCourses() {
        $index = -1;
        $current_letter = null;
        $collection = null;

        $get_all_courses  = "SELECT id, name, url_friendly, parent, empty, url, directory" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE parent IS NULL" . PHP_EOL;
        $get_all_courses .= "AND pending = 0" . PHP_EOL;
        $get_all_courses .= "AND deleted = 0" . PHP_EOL;
        $get_all_courses .= "ORDER BY name ASC";
        
        $get_all_courses_query = Database::$db->query($get_all_courses);
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            $element = new Element($row);
            
            $letter = substr($element->getCourseCode(), 0, 1);

            if ($letter != $current_letter) {
                $current_letter = $letter;
                
                $collection[] = [
                    'letter' => $current_letter,
                    'courses' => []
                ];

                $index++;
            }

            $collection[$index]['courses'][] = $element;
        }

        $this->template->assign('COLLECTION', $collection);
    }
}

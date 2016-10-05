<?php
namespace Youkok2\Processors\Tasks;

use Youkok2\Models\Element;
use Youkok2\Processors\BaseProcessor;
use Youkok2\Utilities\Database;

class LoadCoursesJson extends BaseProcessor
{

    protected function requireDatabase() {
        return true;
    }
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function run() {
        $courses = [];
        
        $get_all_courses  = "SELECT id" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE parent IS NULL" . PHP_EOL;
        $get_all_courses .= "ORDER BY name ASC";
        
        $get_all_courses_query = Database::$db->query($get_all_courses);
        
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            $element = Element::get($row['id']);

            $courses[] = [
                'course' => $element->getCourseCode() . ' - ' . $element->getCourseName(),
                'url' => $element->getFullUrl()];

            unset($element);
        }
        
        file_put_contents(CACHE_PATH . '/courses.json', json_encode($courses));
        
        file_put_contents(CACHE_PATH . '/typeahead.json', json_encode(['timestamp' => time()]));
    }
}

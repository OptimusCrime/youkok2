<?php
/*
 * File: LoadCoursesJson.php
 * Holds: Puts Courses in a JSON file
 * Created: 29.01.15
 * Project: Youkok2
 *
 */

namespace Youkok2\Processors\Tasks;

use \Youkok2\Models\Element as Element;
use \Youkok2\Processors\BaseProcessor as BaseProcessor;
use \Youkok2\Utilities\Database as Database;

class LoadCoursesJson extends BaseProcessor {

    /*
     * Override
     */

    protected function requireDatabase() {
        return true;
    }

    /*
     * Constructor
     */

    public function __construct($method, $settings) {
        // Calling Base' constructor
        parent::__construct($method, $settings);
    }
    
    /*
     * Fetch all courses and build a search file
     */
    
    public function run() {
        $courses = [];
        
        // Build query
        $get_all_courses  = "SELECT id" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE parent IS NULL" . PHP_EOL;
        $get_all_courses .= "ORDER BY name ASC";
        
        // Run query
        $get_all_courses_query = Database::$db->query($get_all_courses);
        
        // Append to array
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            $element = Element::get($row['id']);

            // Append to array
            $courses[] = array(
                'course' => $element->getCourseCode() . ' - ' . $element->getCourseName(),
                'url' => $element->getFullUrl());
        }
        
        // Put content to file
        file_put_contents(CACHE_PATH . '/courses.json', json_encode($courses));
        
        // Store timestamp in typehead.json
        file_put_contents(CACHE_PATH . '/typeahead.json', json_encode(['timestamp' => time()]));
    }
} 
<?php
/*
 * File: LoadCoursesJson.php
 * Holds: Puts Courses in a JSON file
 * Created: 29.01.15
 * Project: Youkok2
*/

namespace Youkok2\Processors\Tasks;

/*
 * Define what classes to use
 */

use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Processors\BaseProcessor as BaseProcessor;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Utilities as Utilities;

/*
 * LoadCoursesJson extending Base
 */

class LoadCoursesJson extends BaseProcessor {

    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        parent::__construct($returnData);
        
        if (self::requireCli() or self::requireAdmin()) {
            // Check database
            if ($this->makeDatabaseConnection()) {
                // Build search file
                $this->buildSearchFile();
                
                // Close database connection
                Database::close();
            }
            else {
                $this->setError();
            }
        }
        else {
            // No access
            $this->noAccess();
        }
        
        // Return data
        $this->returnData();
    }
    
    /*
     * Fetch all courses and build a search file
     */
    
    private function buildSearchFile() {
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
            $element = ElementCollection::get($row['id']);
            
            // Check if valid Element
            if ($element !== null) {
                // Get course info
                $course = $element->controller->getCourse();
                
                // Append to array
                $courses[] = array('course' => $course['code'] . ' - ' . $course['name'],
                    'url' => $element->getUrlFriendly());
            }
        }
        
        // Put content to file
        file_put_contents(CACHE_PATH . '/courses.json', json_encode($courses));
        
        // Store timestamp in typehead.json
        file_put_contents(CACHE_PATH . '/typeahead.json', json_encode(['timestamp' => time()]));
    }
} 
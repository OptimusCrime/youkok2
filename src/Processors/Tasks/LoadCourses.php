<?php
/*
 * File: LoadCourses.php
 * Holds: Load courses
 * Created: 17.12.14
 * Project: Youkok2
*/

namespace Youkok2\Processors\Tasks;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Course as Course;
use \Youkok2\Models\Element as Element;
use \Youkok2\Processors\Base as Base;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Utilities as Utilities;

/*
 * LoadCourses extending Base
 */

class LoadCourses extends Base {

    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        // Calling Base' constructor
        parent::__construct($returnData);
        // if (self::requireCli() or self::requireAdmin()) {
        if (1 == 1) {
            // Check database
            if ($this->checkDatabase()) {
                // Fetch
                $this->fetchData();
                
                // Build search file
                $this->buildSearchFile();
                
                // Close database connection
                Database::close();
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
     * Check if we can connect to the database
     */

    private function checkDatabase() {
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

    /*
     * Fetch the actual data here
     */

    private function fetchData() {
        // Set code to 200
        $this->setData('code', 200);

        $page = 1;
        $added = [];
        $fetched = 0;
        $new = 0;

        // Fetch the courses
        while (true) {
            
            // Load
            $file = file_get_contents('http://www.ntnu.no/web/studier/emnesok?p_p_id=courselistportlet_WAR_courselistportlet&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=fetch-courselist-as-json&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_pos=1&p_p_col_count=2&semester=' . date('Y') . '&faculty=-1&institute=-1&multimedia=0&english=0&phd=0&courseAutumn=0&courseSpring=0&courseSummer=0&searchQueryString=&pageNo=' . $page . '&season=autumn&sortOrder=%2Btitle&year=');
            $json_result = json_decode($file, true);

            // Clean
            $result = [];
            foreach ($json_result['courses'] as $v) {
                $result[] = ['code' => $v['courseCode'],
                    'name' => $v['courseName'],
                    'url_friendly' => Utilities::urlSafe($v['courseCode'])];
                
                // Inc fetched
                $fetched++;
            }

            // Loop every single course
            foreach ($result as $v) {
                // Check if course is in database
                $check_current_course  = "SELECT id" . PHP_EOL;
                $check_current_course .= "FROM course" . PHP_EOL;
                $check_current_course .= "WHERE code = :code" . PHP_EOL;
                $check_current_course .= "LIMIT 1";
                
                $check_current_course_query = Database::$db->prepare($check_current_course);
                $check_current_course_query->execute(array(':code' => $v['code']));
                $row = $check_current_course_query->fetch(\PDO::FETCH_ASSOC);

                // Check if exists
                if (!isset($row['id'])) {
                    // New course
                    $course = New Course();
                    $course->setCode($v['code']);
                    $course->setName($v['name']);
                    $course->save();
                    
                    // New Element
                    $element = new Element();
                    $element->setname($v['code']);
                    $element->setUrlFriendly($v['url_friendly']);
                    $element->setParent(1);
                    $element->setCourse($course);
                    $element->setLocation(null);
                    $element->setDirectory(true);
                    $element->save();
                    
                    // Add text
                    $added[] = 'Added ' . $v['code'];
                    
                    // Inc added
                    $new++;
                    break;
                }
            }
            
            // Check if more results
            if (count($result) == 100) {
                // More results, increase page
                $page++;
            }
            else {
                // No more results, kill script
                break;
            }
        }
        
        // Set message
        $this->setData('msg', ['Fetched' => $fetched, 'New' => $new, 'Added' => $added]);
    }
    
    /*
     * Fetch all courses and build a search file
     */
    
    private function buildSearchFile() {
        $courses = [];
        
        // Build query
        $get_all_courses  = "SELECT c.code, c.name, a.url_friendly" . PHP_EOL;
        $get_all_courses .= "FROM course c" . PHP_EOL;
        $get_all_courses .= "LEFT JOIN archive AS a ON c.id = a.course" . PHP_EOL;
        $get_all_courses .= "ORDER BY c.code ASC";
        
        // Run query
        $get_all_courses_query = Database::$db->prepare($get_all_courses);
        $get_all_courses_query->execute();
        
        // Append to array
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            $courses[] = array('course' => $row['code'] . ' - ' . $row['name'],
                'url' => $row['url_friendly']);
        }
        
        // Put content to file
        file_put_contents(CACHE_PATH . '/courses.json', json_encode($courses));
        
        // Store timestamp in typehead.json
        file_put_contents(CACHE_PATH . '/typeahead.json', json_encode(['timestamp' => time()]));
    }
} 
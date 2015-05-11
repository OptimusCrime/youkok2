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
        
        if (self::requireCli() or self::requireAdmin()) {
            // Check database
            if ($this->checkDatabase()) {
                // Fetch
                $this->fetchData();
                
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
        $updated = 0;

        // Fetch the courses
        while (true) {
            // Load
            $year = date('Y');
            if (date('m') < 6) {
                $year--;
            }
            
            $file = file_get_contents('http://www.ntnu.no/web/studier/emnesok?p_p_id=courselistportlet_WAR_courselistportlet&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=fetch-courselist-as-json&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_pos=1&p_p_col_count=2&semester=' . $year . '&faculty=-1&institute=-1&multimedia=0&english=0&phd=0&courseAutumn=0&courseSpring=0&courseSummer=0&searchQueryString=&pageNo=' . $page . '&season=spring&sortOrder=%2Btitle&year=');
            $json_result = json_decode($file, true);

            // Clean
            $result = [];
            foreach ($json_result['courses'] as $v) {
                $exam = null;
                
                // Check for exam
                if (isset($v['exam'])) {
                    // Loop all exam entries
                    foreach ($v['exam'] as $exam_data) {
                        // Check if exam exists
                        if (strlen($exam_data['date']) > 0) {
                            // Parse date to timestamp
                            $exam_date_split = explode('-', $exam_data['date']);
                            $date_timestamp = mktime(0, 0, 0, $exam_date_split[1], $exam_date_split[2], $exam_date_split[0]);
                            
                            // Check if date is in the future or not
                            if ($date_timestamp > time()) {
                                // This exam date is in the future. If it is smaller than the current on, add to array
                                if ($exam == null or $date_timestamp < $exam) {
                                    $exam = $date_timestamp;
                                }
                            }
                        }
                    }
                }
                
                // Add to result
                $result[] = ['code' => $v['courseCode'],
                    'name' => $v['courseName'],
                    'url_friendly' => Utilities::urlSafe($v['courseCode']),
                    'exam' => $exam];
                
                // Inc fetched
                $fetched++;
            }
            
            // Loop every single course
            foreach ($result as $v) {
                // Clean
                $insert_name = $v['code'] . '||' . $v['name'];
                
                if ($v['exam'] !== null) {
                    $v['exam'] = date('Y-m-d', $v['exam']) . ' 09:00:00';
                }
                
                // Check if course is in database
                $check_current_course  = "SELECT id" . PHP_EOL;
                $check_current_course .= "FROM archive" . PHP_EOL;
                $check_current_course .= "WHERE name = :name" . PHP_EOL;
                $check_current_course .= "LIMIT 1";
                
                $check_current_course_query = Database::$db->prepare($check_current_course);
                $check_current_course_query->execute(array(':name' => $insert_name));
                $row = $check_current_course_query->fetch(\PDO::FETCH_ASSOC);

                // Check if exists
                if (!isset($row['id'])) {
                    // New Element
                    $element = new Element();
                    $element->setname($insert_name);
                    $element->setUrlFriendly($v['url_friendly']);
                    $element->setParent(null);
                    $element->setAccepted(true);
                    $element->setDirectory(true);
                    
                    // Add exam date (if present)
                    if ($v['exam'] !== null) {
                        $element->setExam($v['exam']);
                    }
                    
                    // Save element
                    $element->save();
                    
                    // Add text
                    $added[] = 'Added ' . $v['code'];
                    
                    // Inc added
                    $new++;
                }
                else {
                    // Exists, check if exam is presented
                    if ($v['exam'] !== null) {
                        
                        // Get object
                        $element = new Element();
                        $element->createById($row['id']);
                        
                        // Check if exam date differs
                        if ($element->controller->wasFound() and $element->getExam() != $v['exam']) {
                            // Update exam
                            $element->setExam($v['exam']);
                            $element->update();
                            
                            // Inc updated
                            $updated++;
                        }
                    }
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
        $this->setData('msg', ['Fetched' => $fetched, 'New' => $new, 'Added' => $added, 'Exams updated' => $updated]);
    }
} 
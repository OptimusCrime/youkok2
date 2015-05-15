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
        
        // Fetch the courses
        $file = file_get_contents('http://www.ime.ntnu.no/api/course/-');
        $json_result = json_decode($file, true);
        
        // Clean data
        foreach ($json_result['course'] as $v) {
            // Add to result
            $result[] = ['code' => $v['code'],
                'name' => $v['norwegianName'],
                'url_friendly' => Utilities::urlSafe($v['code'])
            ];
        }
        
        // Loop every single course
        foreach ($result as $v) {
            // Clean
            $insert_name = $v['code'] . '||' . $v['name'];
            
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
                
                // Save element
                $element->save();
                
                // Add text
                $added[] = 'Added ' . $v['code'];
                
                // Inc added
                $new++;
            }
        }
        
        // Set message
        $this->setData('msg', ['Fetched' => $fetched, 'New' => $new, 'Added' => $added]);
    }
} 
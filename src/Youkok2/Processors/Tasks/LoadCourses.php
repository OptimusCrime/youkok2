<?php
/*
 * File: LoadCourses.php
 * Holds: Load courses
 * Created: 17.12.2014
 * Project: Youkok2
 *
 */

namespace Youkok2\Processors\Tasks;

use Youkok2\Models\Element;
use Youkok2\Processors\BaseProcessor;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;

class LoadCourses extends BaseProcessor {

    /*
     * Override
     */

    protected function checkPermissions() {
        return $this->requireCli() or $this->requireAdmin();
    }
    
    /*
     * Override
     */

    protected function requireDatabase() {
        return true;
    }

    /*
     * Construct
     */

    public function __construct($method, $settings) {
        // Calling Base' constructor
        parent::__construct($method, $settings);
    }
    
    /*
     * Method ran by the processor
     */
    
    public function run() {
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
            $search_name = $v['code'] . '||%';
            $insert_name = $v['code'] . '||' . $v['name'];
            
            // Check if course is in database
            $check_current_course  = "SELECT id" . PHP_EOL;
            $check_current_course .= "FROM archive" . PHP_EOL;
            $check_current_course .= "WHERE name LIKE :name" . PHP_EOL;
            $check_current_course .= "LIMIT 1";
            
            $check_current_course_query = Database::$db->prepare($check_current_course);
            $check_current_course_query->execute(array(':name' => $search_name));
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
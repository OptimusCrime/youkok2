<?php
/*
 * File: SyncCourse.php
 * Holds: Sets course data to elements instead
 * Created: 11.01.15
 * Project: Youkok2
*/

namespace Youkok2\Processors\Tasks\Sync;

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

class SyncCourse extends Base {

    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        // Calling Base' constructor
        parent::__construct($returnData);
        
        if (self::requireCli() or self::requireAdmin()) {
            // Check database
            if ($this->checkDatabase()) {
                // Sync
                $this->syncElements();
                
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
     * Syncs empty courses
     */

    private function syncElements() {
        // Set code to 200
        $this->setData('code', 200);
        $update_num = 0;
        
        $get_all_courses  = "SELECT id, code, name" . PHP_EOL;
        $get_all_courses .= "FROM course";
        
        $get_all_courses_query = Database::$db->prepare($get_all_courses);
        $get_all_courses_query->execute();
        
        // Append to array
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get the archive element for the course (base element)
            $get_archive_for_course  = "SELECT id" . PHP_EOL;
            $get_archive_for_course .= "FROM archive" . PHP_EOL;
            $get_archive_for_course .= "WHERE course = :course" . PHP_EOL;
            $get_archive_for_course .= "LIMIT 1";
            
            $get_archive_for_course_query = Database::$db->prepare($get_archive_for_course);
            $get_archive_for_course_query->execute(array(':course' => $row['id']));
            $element = $get_archive_for_course_query->fetch(\PDO::FETCH_ASSOC);
            
            if (isset($element['id'])) {
                $update_empty  = "UPDATE archive" . PHP_EOL;
                $update_empty .= "SET name = :name" . PHP_EOL;
                $update_empty .= "WHERE id = :id";
                
                $update_empty_query = Database::$db->prepare($update_empty);
                $update_empty_query->execute(array(':name' => $row['code'] . '||' . $row['name'], ':id' => $element['id']));
            }
        }
        
        // Check if we should clear cache
        if ($update_num > 0) {
            $this->runProcessor('tasks/clearcache');
        }
    }
} 
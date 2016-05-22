<?php
/*
 * File: Courses.php
 * Holds: Displays a list of courses
 * Created: 16.05.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Loader;

class Courses extends BaseView
{
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Run the view
     */

    public function run() {
        
        // Turn on caching
        $this->template->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);

        // Set menu
        $this->template->assign('HEADER_MENU', 'ARCHIVE');
        
        // Check if cached
        if (!$this->template->isCached('courses.tpl', $this->path)) {
            // Load content
            $this->loadCourses();
        }

        // Display
        $this->displayAndCleanup('courses.tpl', $this->path);
    }
    
    /*
     * Load courses
     */

    private function loadCourses() {
        // Variables are nice
        $index = -1;
        $current_letter = null;
        $collection = null;

        // Load all the courses
        $get_all_courses  = "SELECT id, name, url_friendly, parent, empty, url, directory" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE parent IS NULL" . PHP_EOL;
        $get_all_courses .= "AND pending = 0" . PHP_EOL;
        $get_all_courses .= "AND deleted = 0" . PHP_EOL;
        $get_all_courses .= "ORDER BY name ASC";
        
        $get_all_courses_query = Database::$db->query($get_all_courses);
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get element
            $element = new Element($row);

            // Find the current letter
            $letter = substr($element->getCourseCode(), 0, 1);

            // Check if we should move to next sub collection
            if ($letter != $current_letter) {
                // Set correct letter
                $current_letter = $letter;

                // Create new sub
                $collection[] = [
                    'letter' => $current_letter,
                    'courses' => []
                ];

                // Increase index
                $index++;
            }

            // Add to collection
            $collection[$index]['courses'][] = $element;
        }

        // Return collection
        $this->template->assign('COLLECTION', $collection);
    }
}

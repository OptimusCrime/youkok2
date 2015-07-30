<?php
/*
 * File: Courses.php
 * Holds: Displays a list of courses
 * Created: 16.05.2015
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Element as Element;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Loader as Loader;

/*
 * The Home class, extending Base class
 */

class Courses extends BaseView {

    /*
     * Constructor
     */

    public function __construct() {
        parent::__construct();
        
        // Turn on caching
        $this->template->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);

        // Set menu
        $this->template->assign('HEADER_MENU', 'ARCHIVE');

        // Check if cached
        if (!$this->template->isCached('courses.tpl', Loader::queryGetClean())) {

            // Load content
            $this->loadCourses();
        }

        // Display
        $this->displayAndCleanup('courses.tpl', Loader::queryGetClean());
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
        $get_all_courses  = "SELECT id, name, url_friendly, parent, empty" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE parent IS NULL" . PHP_EOL;
        $get_all_courses .= "AND is_visible = 1" . PHP_EOL;
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
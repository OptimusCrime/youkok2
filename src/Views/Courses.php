<?php
/*
 * File: Courses.php
 * Holds: Courses view
 * Created: 16.05.2015
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Element as Element;
use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Routes as Routes;
use \Youkok2\Utilities\Utilities as Utilities;


/*
 * The Home class, extending Base class
 */

class Courses extends Base {

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
        if (!$this->template->isCached('courses.tpl', $this->queryGetClean())) {
            // Get title
            $this->template->assign('ARCHIVE_TITLE', '<h1>Emner</h1>');

            // Get breadcrumbs
            $this->template->assign('ARCHIVE_BREADCRUMBS', '<li class="active">Emner</li>');

            // Load content
            $this->loadCourses();
        }

        // Display
        $this->displayAndCleanup('courses.tpl', $this->queryGetClean());
    }
    
    /*
     * Load courses
     */

    private function loadCourses() {
        // Variables are nice
        $ret = '';
        $letter = null;
        $container_is_null = true;
        $new_row = false;

        // Load all the courses
        $get_all_courses  = "SELECT id, name, url_friendly, empty" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE parent IS NULL" . PHP_EOL;
        $get_all_courses .= "AND is_visible = 1" . PHP_EOL;
        $get_all_courses .= "ORDER BY name ASC";
        
        $get_all_courses_query = Database::$db->query($get_all_courses);
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            $element = new Element();
            $element->createById($row['id'], true);
            
            // Override attributes
            $element->setName($row['name']);
            $element->setEmpty($row['empty']);
            $element->setUrlFriendly($row['url_friendly']);
            
            // Check if element is course
            if ($element->controller->isCourse()) {
                // Get course
                $course = $element->controller->getCourse();
                // Store the current letter
                $current_letter = substr($course['code'], 0, 1);

                // Check how we should parse the course
                if ($container_is_null) {
                    $ret .= '<div class="row">' . PHP_EOL;
                    $ret .= '    <div class="col-xs-12 col-md-6 course-box">' . PHP_EOL;
                    $ret .= '        <h3>' . $current_letter . '</h3>' . PHP_EOL;
                    $ret .= '        <ul class="list-group">' . PHP_EOL;

                    $container_is_null = false;
                }
                else {
                    if ($letter != $current_letter) {
                        $ret .= '        </ul>' . PHP_EOL;
                        $ret .= '    </div>' . PHP_EOL;
                        
                        if ($new_row) {
                            $ret .= '</div>' . PHP_EOL;
                            $ret .= '<div class="row">' . PHP_EOL;
                        }
                        
                        $new_row = !$new_row;
                        
                        $ret .= '    <div class="col-xs-12 col-md-6 course-box">' . PHP_EOL;
                        $ret .= '        <h3>' . $current_letter . '</h3>' . PHP_EOL;
                        $ret .= '        <ul class="list-group">' . PHP_EOL;
                    }
                }

                $ret .= '            <li class="' . (($element->isEmpty()) ? 'course-empty ' : '') . 'list-group-item">' . PHP_EOL;
                $ret .= '                <a href="' . $element->controller->generateUrl(Routes::ARCHIVE) . '"><strong>' . $course['code'] . '</strong> &mdash; ' . $course['name'] . '</a>' . PHP_EOL;
                $ret .= '            </li>' . PHP_EOL;
                
                // Assign new letter
                $letter = $current_letter;
            }
        }

        // End container
        $ret .= '        </ul>' . PHP_EOL;
        $ret .= '    </div>' . PHP_EOL;
        $ret .= '</div>' . PHP_EOL;

        // Return content
        $this->template->assign('ARCHIVE_DISPLAY', $ret);
    }
}
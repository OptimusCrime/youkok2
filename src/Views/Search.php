<?php
/*
 * File: Search.php
 * Holds: Handles searches by the user
 * Created: 06.08.2014
 * Project: Youkok2
 * 
*/

namespace Youkok2\Views;

use \Youkok2\Models\Element as Element;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Routes as Routes;

class Search extends BaseView {

    /*
     * Constructor
     */

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();

        // Set menu
        $this->template->assign('HEADER_MENU', '');

        if (!isset($_GET['s']) or strlen($_GET['s']) == 0) {
            $this->template->assign('SEARCH_MODE', 'info');
        }
        else {
            $this->search();
        }
        
        // Display
        $this->displayAndCleanup('search.tpl');
    }

    /*
     * Method for searching for courses
     */

    private function search () {
        // Variable to keep track of the results
        $collection = [];

        // Assign the search
        $this->template->assign('SEARCH_MODE', 'search');
        $this->template->assign('SEARCH_QUERY', $_GET['s']);

        // Clean the input
        $input = explode(' ', $_GET['s']);
        if (count($input) > 0) {
            $input_clean = array();
            foreach ($input as $v) {
                if (strlen(str_replace('*', '', $v)) > 0) {
                    $input_clean[] = str_replace('*', '%', $v);
                }
            }
        }

        // Check if anything was clean as fuck
        if (count($input_clean) > 0) {

            $course_code = array();
            $course_name = array();

            // Search by course code
            foreach ($input_clean as $v) {
                $search_by_code = "SELECT id" . PHP_EOL;
                $search_by_code .= "FROM archive" . PHP_EOL;
                $search_by_code .= "WHERE name LIKE :query" . PHP_EOL;
                $search_by_code .= "AND parent IS NULL";

                $search_by_code_query = Database::$db->prepare($search_by_code);
                $search_by_code_query->execute(array(':query' => '%' . $v . '%\|\|%'));
                while ($row = $search_by_code_query->fetch(\PDO::FETCH_ASSOC)) {
                    if (!in_array($row['id'], $course_code)) {
                        $course_code[] = $row['id'];
                    }
                }
            }

            foreach ($input_clean as $v) {
                $search_by_name = "SELECT id" . PHP_EOL;
                $search_by_name .= "FROM archive" . PHP_EOL;
                $search_by_name .= "WHERE name LIKE :query" . PHP_EOL;
                $search_by_name .= "AND parent IS NULL";

                $search_by_name_query = Database::$db->prepare($search_by_name);
                $search_by_name_query->execute(array(':query' => '%\|\|%' . $v . '%'));
                while ($row = $search_by_name_query->fetch(\PDO::FETCH_ASSOC)) {
                    if (!in_array($row['id'], $course_name)) {
                        $course_name[] = $row['id'];
                    }
                }
            }

            // Check if anything was matched twice
            $search_results = array();
            if (count($course_code) > 0) {
                foreach ($course_code as $v) {
                    if (!array_key_exists($v, $search_results)) {
                        $search_results[$v] = 1;
                    } else {
                        $search_results[$v]++;
                    }
                }
            }
            if (count($course_name) > 0) {
                foreach ($course_name as $v) {
                    if (!array_key_exists($v, $search_results)) {
                        $search_results[$v] = 1;
                    } else {
                        $search_results[$v]++;
                    }
                }
            }

            // Check if anything was found at all!
            if (count($search_results) > 0) {
                // Sort resuslts
                arsort($search_results);

                // Get the final results
                $ret = '';
                $num = 0;

                // Loop all the search results
                foreach ($search_results as $k => $v) {
                    // Create new element
                    $element = new Element();
                    $element->createById($k);

                    // Check if element was found
                    if ($element->wasFound()) {
                        // Increase number of hits
                        $num++;

                        // Highlight names
                        $match_names = [$element->getCourseCode(), $element->getCourseName()];
                        for ($i = 0; $i <= 1; $i++) {
                            foreach ($input_clean as $iv) {
                                $iv_clean = str_replace('%', '.', $iv);
                                if (preg_match('/^' . $iv_clean . '/i', $match_names[$i])) {
                                    $match_names[$i] = preg_replace('/^' . str_replace('%', '(.*)', $iv) . '/i', '<strong>${0}</strong>', $match_names[$i]);
                                    break;
                                }
                            }
                        }

                        // Set data
                        $element->setName($match_names[0] . '||' . $match_names[1]);

                        // Store to collection
                        $collection[] = $element;
                    }
                }
            }
        }

        // Assign variables
        $this->template->assign('ELEMENTS', $collection);
    }
}
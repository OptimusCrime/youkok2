<?php
/*
 * File: search.controller.php
 * Holds: The SearchController-class
 * Created: 06.08.14
 * Project: Youkok2
 * 
*/

//
// The SearchController class
//

class SearchController extends Youkok2 {

    //
    // The constructor for this subclass
    //

    public function __construct($routes) {
        // Calling Base' constructor
        parent::__construct($routes);
        
        if (!isset($_GET['s']) or strlen($_GET['s']) == 0) {
            $this->template->assign('SEARCH_MODE', 'info');
        }
        else {
            $this->search();
        }
        
        // Display
        $this->displayAndCleanup('search.tpl');
    }

    //
    // Method for searching for courses
    //

    private function search () {
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
            // Search by course code
            $course_code = array();
            foreach ($input_clean as $v) {
                $search_by_code = "SELECT id
                FROM course
                WHERE code LIKE :query";
                
                $search_by_code_query = $this->db->prepare($search_by_code);
                $search_by_code_query->execute(array(':query' => $v));
                while ($row = $search_by_code_query->fetch(PDO::FETCH_ASSOC)) {
                    if (!in_array($row['id'], $course_code)) {
                        $course_code[] = $row['id'];
                    }
                }
            }

            // Search by course name
            $course_name = array();
            if (count($course_code) > 0) {
                foreach ($input_clean as $v) {
                    $search_by_name = "SELECT id
                    FROM course
                    WHERE name LIKE :query
                    AND id IN (" . implode(',', $course_code) . ")";
                    
                    $search_by_name_query = $this->db->prepare($search_by_name);
                    $search_by_name_query->execute(array(':query' => $v));
                    while ($row = $search_by_name_query->fetch(PDO::FETCH_ASSOC)) {
                        if (!in_array($row['id'], $course_name)) {
                            $course_name[] = $row['id'];
                        }
                    }
                }
            }
            else {
                foreach ($input_clean as $v) {
                    $search_by_name = "SELECT id
                    FROM course
                    WHERE name LIKE :query";
                    
                    $search_by_name_query = $this->db->prepare($search_by_name);
                    $search_by_name_query->execute(array(':query' => $v));
                    while ($row = $search_by_name_query->fetch(PDO::FETCH_ASSOC)) {
                        if (!in_array($row['id'], $course_name)) {
                            $course_name[] = $row['id'];
                        }
                    }
                }
            }

            // Check if anything was matched twice
            $search_results = array();
            if (count($course_code) > 0) {
                foreach ($course_code as $v) {
                    if (!array_key_exists($v, $search_results)) {
                        $search_results[$v] = 1;
                    }
                    else {
                        $search_results[$v]++;
                    }
                }
            }
            if (count($course_name) > 0) {
                foreach ($course_name as $v) {
                    if (!array_key_exists($v, $search_results)) {
                        $search_results[$v] = 1;
                    }
                    else {
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

                foreach ($search_results as $k => $v) {
                    $get_seach_object = "SELECT id
                    FROM archive
                    WHERE course = :course
                    AND is_visible = 1";
                    
                    $get_seach_object_query = $this->db->prepare($get_seach_object);
                    $get_seach_object_query->execute(array(':course' => $k));
                    $get_seach_object_result = $get_seach_object_query->fetch(PDO::FETCH_ASSOC);
                    if (isset($get_seach_object_result['id'])) {
                        // Create object
                        $element = new Item($this);
                        $element->createById($get_seach_object_result['id']);
                        if ($element->wasFound()) {
                            // Increase number of hits
                            $num++;

                            // Highlight names
                            $match_names = array($element->getName(), $element->getCourse()->getName());
                            for ($i = 0; $i <= 1; $i++) {
                                foreach ($input_clean as $iv) {
                                    $iv_clean = str_replace('%', '.', $iv);
                                    if (preg_match('/^' . $iv_clean . '/i', $match_names[$i])) {
                                        $match_names[$i] = preg_replace('/^' . str_replace('%', '(.*)', $iv) . '/i', '<strong>${0}</strong>', $match_names[$i]);
                                        break;
                                    }
                                }
                            }                            

                            // Build string
                            $ret .= '<p><a href="' . $element->generateUrl($this->routes['archive'][0]) . '">' . $match_names[0] . ' - ' . $match_names[1] . '</a></p>';
                        }
                    }
                }

                // Assign template variables
                $this->template->assign('SEARCH_NUM', number_format($num));
                $this->template->assign('SEARCH_RESULT', $ret);
            }
            else {
                // No results
                $this->template->assign('SEARCH_NUM', 'ingen');
                $this->template->assign('SEARCH_RESULT', 'Ditt søk returnerte ingen treff!');
            }
        }
        else {
            $this->template->assign('SEARCH_NUM', 'ingen');
            $this->template->assign('SEARCH_RESULT', 'Ditt søk returnerte ingen treff!');
        }
    }
}

//
// Return the class name
//

return 'SearchController';
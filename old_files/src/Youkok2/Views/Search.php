<?php
namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Routes;

class Search extends BaseView
{
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        $this->template->assign('HEADER_MENU', '');

        if (!isset($_GET['s']) or strlen($_GET['s']) == 0) {
            $this->template->assign('SEARCH_MODE', 'info');
        }
        else {
            $this->search();
        }
        
        $this->displayAndCleanup('search.tpl');
    }

    private function search() {
        $collection = [];

        $this->template->assign('SEARCH_MODE', 'search');
        $this->template->assign('SEARCH_QUERY', $_GET['s']);

        $input = explode(' ', $_GET['s']);
        if (count($input) > 0) {
            $input_clean = [];
            foreach ($input as $v) {
                if (strlen(str_replace('*', '', $v)) > 0) {
                    $input_clean[] = str_replace('*', '%', $v);
                }
            }
        }

        if (count($input_clean) > 0) {
            $course_code = [];
            $course_name = [];

            foreach ($input_clean as $v) {
                $search_by_code = "SELECT id" . PHP_EOL;
                $search_by_code .= "FROM archive" . PHP_EOL;
                $search_by_code .= "WHERE name LIKE :query" . PHP_EOL;
                $search_by_code .= "AND parent IS NULL" . PHP_EOL;
                $search_by_code .= "AND pending = 0" . PHP_EOL;
                $search_by_code .= "AND deleted = 0";

                $search_by_code_query = Database::$db->prepare($search_by_code);
                $search_by_code_query->execute([':query' => '%' . $v . '%\|\|%']);
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
                $search_by_name .= "AND parent IS NULL" . PHP_EOL;
                $search_by_name .= "AND pending = 0" . PHP_EOL;
                $search_by_name .= "AND deleted = 0";

                $search_by_name_query = Database::$db->prepare($search_by_name);
                $search_by_name_query->execute([':query' => '%\|\|%' . $v . '%']);
                while ($row = $search_by_name_query->fetch(\PDO::FETCH_ASSOC)) {
                    if (!in_array($row['id'], $course_name)) {
                        $course_name[] = $row['id'];
                    }
                }
            }

            // Check if anything was matched twice
            $search_results = [];
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

            if (count($search_results) > 0) {
                arsort($search_results);

                $ret = '';
                $num = 0;

                foreach ($search_results as $k => $v) {
                    $element = new Element();
                    $element->createById($k);

                    if ($element->wasFound()) {
                        $num++;

                        // Highlight names
                        $match_names = [$element->getCourseCode(), $element->getCourseName()];
                        for ($i = 0; $i <= 1; $i++) {
                            foreach ($input_clean as $iv) {
                                $iv_clean = str_replace('%', '.', $iv);
                                if (preg_match('/^' . $iv_clean . '/i', $match_names[$i])) {
                                    $match_names[$i] = preg_replace(
                                        '/^' . str_replace('%', '(.*)', $iv) . '/i',
                                        '<strong>${0}</strong>',
                                        $match_names[$i]
                                    );
                                    break;
                                }
                            }
                        }

                        $element->setName($match_names[0] . '||' . $match_names[1]);

                        $collection[] = $element;
                    }
                }
            }
        }

        $this->template->assign('ELEMENTS', $collection);
    }
}

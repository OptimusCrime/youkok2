<?php


namespace Youkok2\Processors\Tasks;

/*
 * Define what classes to use
 */

use \Youkok2\Processors\Base as Base;
use \Youkok2\Utilities\Database as Database;

/*
 * Class
 */

class LoadCourses extends Base {

    /*
     * Constructor
     */

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();

        // Check database
        if ($this->checkDatabase()) {
            // Fetch
            $this->fetchData();
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
            $this->setData('msg', 'Could not connect to database');
            $this->setData('code', 500);

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
        $message = array();

        // Fetch the courses
        while (true) {
            // Load
            $file = file_get_contents('http://www.ntnu.no/web/studier/emnesok?p_p_id=courselistportlet_WAR_courselistportlet&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=fetch-courselist-as-json&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_pos=1&p_p_col_count=2&semester=' . date('Y') . '&faculty=-1&institute=-1&multimedia=0&english=0&phd=0&courseAutumn=0&courseSpring=0&courseSummer=0&searchQueryString=&pageNo=' . $page . '&season=autumn&sortOrder=%2Btitle&year=');
            $json_content = json_decode($file, true);

            // Clean
            foreach ($json_content['courses'] as $v) {
                $clean_url_path = url_friendly($v['courseCode']);
                $clean[] = array('code' => $v['courseCode'],
                    'name' => $v['courseName'],
                    'url_friendly' => $clean_url_path,
                    'directory' => $clean_url_path);
            }

            // Loop every single course
            foreach ($clean as $v) {
                // Check if course is in database
                $check_current_course = "SELECT id
            FROM course
            WHERE code = :code
            LIMIT 1";

            $check_current_course_query = Database::$db->prepare($check_current_course);
            $check_current_course_query->execute(array(':code' => $v['code']));
            $row = $check_current_course_query->fetch(\PDO::FETCH_ASSOC);

            // Check if exists
            if (!isset($row['id'])) {
                $message[] = 'Added ' . $v['code'];
                /*
                // Check if url-friendly or name exists
                $check_current_course2 = "SELECT id
                FROM archive
                WHERE (
                    name = :name
                    OR url_friendly = :url_friendly
                )
                AND parent = 1
                LIMIT 1";

                $check_current_course2_query = Database::$db->prepare($check_current_course2);
                $check_current_course2_query->execute(array(':name' => $v['code'],
                    ':url_friendly' => $v['url_friendly']));
                $row2 = $check_current_course2_query->fetch(\PDO::FETCH_ASSOC);

                // Check if exists
                if (!isset($row2['id'])) {
                    // Check if the directory exists
                    $directory_check = BASE_PATH . FILE_ROOT . '/' . $v['directory'];
                    if (!is_dir($directory_check)) {
                        // Insert course
                        $insert_course = "INSERT INTO course (code, name)
                        VALUES (:code, :name)";

                        $insert_course_query = $db->prepare($insert_course);
                        $insert_course_query->execute(array(':code' => $v['code'], ':name' => $v['name']));

                        // Get the course-id
                        $course_id = $db->lastInsertId();

                        // Build empty archive
                        $insert_archive = "INSERT INTO archive (name, url_friendly, parent, course, location, is_directory)
                        VALUES (:name, :url_friendly, :parent, :course, :location, :is_directory)";

                        $insert_archive_query = $db->prepare($insert_archive);
                        $insert_archive_query->execute(array(':name' => $v['code'],
                            ':url_friendly' => $v['url_friendly'],
                            ':parent' => 1,
                            ':course' => $course_id,
                            ':location' => $v['directory'],
                            ':is_directory' => 1));

                        // Create directory
                        mkdir($directory_check);
                    }*/
                }
            }
        }

        $this->setData('message', $message);
    }
} 
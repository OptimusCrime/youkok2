<?php
/*
 * File: CourseController.php
 * Holds: Controller for the model Course
 * Created: 06.11.2014
 * Project: Youkok2
*/

namespace Youkok2\Models\Controllers;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Course as Course;
use \Youkok2\Utilities\CacheManager as CacheManager;
use \Youkok2\Utilities\Database as Database;

/*
 * The class CourseController
 */

class CourseController implements BaseController {
    
    /*
     * Variables
     */
    
    private $model;
    
    /*
     * Constructor
     */
    
    public function __construct($model) {
        // Set pointer to the model
        $this->model = $model;
    }
    
    /*
     * Create
     */
    
    public function createById($id) {
        // Check if cached
        if (CacheManager::isCached($id, 'c')) {
            // Get the cached array
            $temp_cache_data = CacheManager::getCache($id, 'c');

            // Fields to look up
            $fields = ['name', 'code'];
            
            // Loop all the fields and apply data
            foreach ($temp_cache_data as $k => $v) {
                 $k_actual = 'set' . ucfirst($k);
                // Check that the field exists as a property/attribute in this class
                if (method_exists('\Youkok2\Models\Course', $k_actual)) {
                    call_user_func_array(array($this->model, $k_actual), array($v));
                }
            }
        }
        else {
            $get_course = "SELECT name, code
            FROM course
            WHERE id = :id";
            
            $get_course_query = Database::$db->prepare($get_course);
            $get_course_query->execute(array(':id' => $id));
            $row = $get_course_query->fetch(\PDO::FETCH_ASSOC);
            
            // Check if anything was returned
            if (isset($row['name'])) {
                // Store information
                $this->model->setId($id);
                $this->model->setCode($row['code']);
                $this->model->setName($row['name']);
                
                // Cache the new result
                $this->cache();
            }
        }
    }
    
    /*
     * Create cache string for this Course
     */

    private function cacheFormat() {
        $cache_temp = [];
        $fields = ['getCode', 'getName'];
        
        // Loop each field
        foreach ($fields as $v) {
            $v_pretty = strtolower(str_replace(['get', 'is'], '', $v));
            if (method_exists('\Youkok2\Models\Course', $v)) {
                $cache_temp[] = "'" . $v_pretty . "' => '" . addslashes(call_user_func(array($this->model, $v))) . "'";
            }
        }
         
        // Implode and return
        return implode(', ', $cache_temp);
    }
    
    /*
     * Save method (for a new Course)
     */
    
    public function save() {
        $insert_course  = "INSERT INTO course (code, name) " . PHP_EOL;
        $insert_course .= "VALUES (:code, :name)";

        $insert_course_query = Database::$db->prepare($insert_course);
        $insert_course_query->execute([':code' => $this->model->getCode(),
            ':name' => $this->model->getName()]);

        // Get the course-id
        $course_id = Database::$db->lastInsertId();
        
        // Set id to model
        $this->model->setId($course_id);
        
        // Cache
        $this->cache();
    }
    
    /*
     * Update method
     */
    
    public function update() {
        // TODO
    }
    
    /*
     * Cache element
     */
    
    public function cache() {
        CacheManager::setCache($this->model->getId(), 'c', $this->cacheFormat());
    }
}
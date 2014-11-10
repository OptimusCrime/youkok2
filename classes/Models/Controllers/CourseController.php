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

class CourseController {
    
    /*
     * Variables
     */
    
    private $model;
    
    /*
     * Constructor
     */
    
    public function __construct(&$model) {
        // Set pointer to the model
        $this->model = $model;
    }
}
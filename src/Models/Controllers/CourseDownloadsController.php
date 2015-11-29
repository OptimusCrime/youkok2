<?php
/*
 * File: CourseDownloadsController.php
 * Holds: Controller for the model CourseDownloads
 * Created: 14.07.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\Controllers;

class CourseDownloadsController extends BaseController {
    
    /*
     * Variables
     */
    
    public static $cacheKey = 'cd';
    
    /*
     * Constructor
     */

    public function __construct($model) {
        parent::__construct($this, $model);
    }
}
<?php
/*
 * File: CourseDownloadsController.php
 * Holds: Controller for the model CourseDownloads
 * Created: 14.07.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\Controllers\Cache;

use Youkok2\Models\Controllers\BaseController;

class CourseDownloadsController extends BaseController {
    
    /*
     * Intervals for the query
     */
    
    public static $timeIntervals = [
        '', // All
        'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.is_visible = 1', // Day
        'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 WEEK) AND a.is_visible = 1', // Week 
        'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND a.is_visible = 1', // Month
        'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 YEAR) AND a.is_visible = 1', // Year
    ];
    
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
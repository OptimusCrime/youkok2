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

class CourseDownloadsController extends BaseController 
{
    
    /*
     * Intervals for the query
     */
    
    public static $timeIntervals = [
        '', // All
        'WHERE d.downloaded_time >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 DAY) AND a.pending = 0 AND a.deleted = 0', // Day
        'WHERE d.downloaded_time >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 WEEK) AND a.pending = 0 AND a.deleted = 0', // Week 
        'WHERE d.downloaded_time >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 MONTH) AND a.pending = 0 AND a.deleted = 0', // Month
        'WHERE d.downloaded_time >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 YEAR) AND a.pending = 0 AND a.deleted = 0', // Year
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

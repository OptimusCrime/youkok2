<?php
/*
 * File: CourseDownloadsController.php
 * Holds: Controller for the model CourseDownloads
 * Created: 14.07.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\Controllers\Cache;

class CourseDownloadsController extends CacheModelController
{
    
    /*
     * Intervals for the query
     */
    
    public static $timeIntervals = [
        'mysql' => [
            // All
            'WHERE a.pending = 0 AND a.deleted = 0',

            // Day
            'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.pending = 0 AND a.deleted = 0',

            // Week
            'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND a.pending = 0 AND a.deleted = 0',

            // Month
            'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND a.pending = 0 AND a.deleted = 0',

            // Year
            'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 YEAR) AND a.pending = 0 AND a.deleted = 0',
        ],
        'sqlite' => [
            // All
            'WHERE a.pending = 0 AND a.deleted = 0',

            // Day
            'WHERE d.downloaded_time >= datetime("now", "-1 day") AND a.pending = 0 AND a.deleted = 0',

            // Week
            'WHERE d.downloaded_time >= datetime("now", "-7 days")  AND a.pending = 0 AND a.deleted = 0',

            // Month
            'WHERE d.downloaded_time >= datetime("now", "-1 month")  AND a.pending = 0 AND a.deleted = 0',

            // Year
            'WHERE d.downloaded_time >= datetime("now", "-1 year")  AND a.pending = 0 AND a.deleted = 0',
        ]
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

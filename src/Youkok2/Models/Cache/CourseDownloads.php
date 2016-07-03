<?php
/*
 * File: CourseDownloads.php
 * Holds: Holds data for a course download count
 * Created: 29.11.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\Cache;

class CourseDownloads extends CacheModel
{
    protected $controllerClass = \Youkok2\Models\Controllers\Cache\CourseDownloadsController::class;
}

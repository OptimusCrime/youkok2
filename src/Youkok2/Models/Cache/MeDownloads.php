<?php
/*
 * File: MeDownloads.php
 * Holds: Holds data for personal downloads
 * Created: 12.12.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\Cache;

class MeDownloads extends CacheModel
{
    protected $controllerClass = \Youkok2\Models\Controllers\Cache\MeDownloadsController::class;

    public static function __callStatic($name, $arguments) {
        // Check if method exists
        if (method_exists('Youkok2\Models\StaticControllers\Cache\MeDownloadsStaticController', $name)) {
            // Call method and return response
            return call_user_func_array(['Youkok2\Models\StaticControllers\Cache\MeDownloadsStaticController',
                $name], $arguments);
        }
    }
}

<?php
namespace Youkok2\Models\Cache;

class MeDownloads extends CacheModel
{
    protected $controllerClass = \Youkok2\Models\Controllers\Cache\MeDownloadsController::class;

    public static function __callStatic($name, $arguments) {
        if (method_exists('Youkok2\Models\StaticControllers\Cache\MeDownloadsStaticController', $name)) {
            return call_user_func_array(['Youkok2\Models\StaticControllers\Cache\MeDownloadsStaticController',
                $name], $arguments);
        }
    }
}

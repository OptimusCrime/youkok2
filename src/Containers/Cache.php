<?php
namespace Youkok\Containers;

use \Slim\Container;

class Cache
{
    public static function load(Container $container)
    {
        $cache = null;
        try {
            $cacheSettings = $container->get('settings')['cache'];

            $cache = new \Redis();
            $cache->connect($cacheSettings['host'], $cacheSettings['port']);
        }
        catch (\RedisException $e) {
            //
        }

        $container['cache'] = function ($container) use ($cache) {


            return $cache;
        };
    }
}

<?php
declare(strict_types=1);

namespace Youkok\Containers;

use \Slim\Container;
use \Illuminate\Cache\DatabaseStore;

use Youkok\Helpers\JsonEncrypter;

class Cache
{
    public static function load(Container $container)
    {
        $connection = $container->get('db')->getConnection();

        $container['cache'] = function ($container) use ($connection) {
            $cache = new DatabaseStore($connection, new JsonEncrypter(), 'cache');

            return $cache;
        };
    }
}

<?php
namespace Youkok\Common\Containers;

use Redis;
use RedisException;
use Psr\Container\ContainerInterface;

class Cache implements ContainersInterface
{
    public static function load(ContainerInterface $container): void
    {
        $container['cache'] = function () use ($container): ?Redis {
            $cache = null;

            try {
                $cache = new Redis();
                $cache->connect(getenv('CACHE_HOST'), getenv('CACHE_PORT'));

                return $cache;
            } catch (RedisException $ex) {
                $logger = $container->get(Logger::class);
                $logger->error($ex);
            }

            return $cache;
        };
    }
}

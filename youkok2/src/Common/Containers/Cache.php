<?php
namespace Youkok\Common\Containers;

use Redis;
use RedisException;
use Psr\Container\ContainerInterface;

use Youkok\Helpers\Configuration\Configuration;

class Cache implements ContainersInterface
{
    public static function load(ContainerInterface $container): void
    {
        $container['cache'] = function () use ($container): ?Redis {
            $cache = null;
            $configuration = Configuration::getInstance();

            try {
                $cache = new Redis();
                $cache->connect(
                    $configuration->getCacheHost(),
                    $configuration->getCachePort()
                );

                return $cache;
            } catch (RedisException $ex) {
                $logger = $container->get(Logger::class);
                $logger->error($ex);
            }

            return $cache;
        };
    }
}

<?php
namespace Youkok\Common\Containers;

use DI\Container;
use Exception;
use Redis;
use RedisException;

use Youkok\Helpers\Configuration\Configuration;

class Cache implements ContainerInterface
{
    public static function load(Container $container): void
    {
        $cache = null;
        $configuration = Configuration::getInstance();

        try {
            $cache = new Redis();
            $cache->connect(
                $configuration->getRedisHost(),
                $configuration->getCachePort()
            );

            $container->set('cache', $cache);
        } catch (RedisException $ex) {
            try {
                $container->get('logger')->error($ex);
            }
            catch (Exception $ex) {
                // Oh well
            }
        }

        $container->set('cache', $cache);
    }
}

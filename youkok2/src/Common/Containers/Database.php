<?php
namespace Youkok\Common\Containers;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\ConnectionResolver;

use Youkok\Helpers\Configuration\Configuration;

class Database implements ContainersInterface
{
    public static function load(ContainerInterface $container): void
    {
        $configuration = Configuration::getInstance();

        $connection = [
            'driver' => 'mysql',
            'host' => $configuration->getMysqlHost(),
            'username' => $configuration->getMysqlUser(),
            'password' => $configuration->getMysqlPassword(),
            'database' => $configuration->getMysqlDatabase(),
            'port' => $configuration->getMysqlPort(),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ];

        $capsule = new Manager;
        $capsule->addConnection($connection);

        $capsule->setEventDispatcher(new Dispatcher(new Container()));

        // Make it possible to use $app->get('db') -> whatever
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        // Make it possible to use Model :: whatever
        $resolver = new ConnectionResolver();
        $resolver->addConnection('default', $capsule->getConnection());
        $resolver->setDefaultConnection('default');
        Model::setConnectionResolver($resolver);

        if ($configuration->isDev()) {
            DB::connection()->enableQueryLog();
        }

        $container['db'] = function () use ($capsule): Manager {
            return $capsule;
        };
    }
}

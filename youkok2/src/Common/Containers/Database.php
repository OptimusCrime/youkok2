<?php
namespace Youkok\Common\Containers;

use DI\Container;
use Illuminate\Container\Container as DispatchContainer;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\ConnectionResolver;

use Youkok\Helpers\Configuration\Configuration;

class Database
{
    public static function load(Container $container): void
    {
        $configuration = Configuration::getInstance();

        $connection = [
            'driver' => 'pgsql',
            'host' => $configuration->getDbHost(),
            'username' => $configuration->getDbUser(),
            'password' => $configuration->getDbPassword(),
            'database' => $configuration->getDbDatabase(),
            'port' => $configuration->getDbPort(),
        ];

        $capsule = new Manager;
        $capsule->addConnection($connection);

        $capsule->setEventDispatcher(new Dispatcher(new DispatchContainer()));

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

        $container->set(Database::class, $capsule);
    }
}

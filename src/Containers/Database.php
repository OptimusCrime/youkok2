<?php
declare(strict_types=1);

namespace Youkok\Containers;

use \Slim\Container;
use \Illuminate\Database\Capsule\Manager;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\ConnectionResolver;

class Database
{
    public static function load(Container $container)
    {
        $capsule = new Manager;
        $capsule->addConnection($container->get('settings')['db']);

        // Make it possible to use $app->get('db') -> whatever
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        // Make it possible to use Model :: whatever
        $resolver = new ConnectionResolver();
        $resolver->addConnection('default', $capsule->getConnection());
        $resolver->setDefaultConnection('default');
        Model::setConnectionResolver($resolver);

        $container['db'] = function ($container) use ($capsule) {
            return $capsule;
        };
    }
}

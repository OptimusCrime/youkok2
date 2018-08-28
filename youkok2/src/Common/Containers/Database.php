<?php
namespace Youkok\Common\Containers;

use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\ConnectionResolver;

class Database implements ContainersInterface
{
    public static function load(ContainerInterface $container)
    {
        $connection = [
            'driver' => 'mysql',
            'host' => getenv('DATABASE_HOST'),
            'database' => getenv('DATABASE_TABLE'),
            'username' => getenv('DATABASE_USER'),
            'password' => getenv('DATABASE_PASSWORD'),
            'port' => getenv('MYSQL_PORT'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ];

        $capsule = new Manager;
        $capsule->addConnection($connection);

        // Make it possible to use $app->get('db') -> whatever
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        // Make it possible to use Model :: whatever
        $resolver = new ConnectionResolver();
        $resolver->addConnection('default', $capsule->getConnection());
        $resolver->setDefaultConnection('default');
        Model::setConnectionResolver($resolver);

        $container['db'] = function () use ($capsule) {
            return $capsule;
        };
    }
}

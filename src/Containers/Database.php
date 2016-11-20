<?php
declare(strict_types=1);

namespace Youkok\Containers;

use \Slim\Container;
use \Illuminate\Database\Capsule\Manager;

class Database
{
    public static function load(Container $container)
    {
        $container['db'] = function ($container) {
            $capsule = new Manager;
            $capsule->addConnection($container['settings']['db']);

            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            return $capsule;
        };
    }
}

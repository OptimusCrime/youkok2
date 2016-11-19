<?php
declare(strict_types=1);

namespace Youkok\Loaders;

use \Slim\Container;

class Containers
{
    public static function load(Container $appContainer, array $containers)
    {
        foreach ($containers as $container) {
            call_user_func([$container, 'load'], $appContainer);
        }
    }
}

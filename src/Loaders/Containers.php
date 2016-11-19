<?php
declare(strict_types=1);

namespace Youkok\Loaders;

use \Interop\Container\ContainerInterface;

class Containers
{
    public static function load(ContainerInterface $appContainer, array $containers)
    {
        foreach ($containers as $container) {
            call_user_func([$container, 'load'], $appContainer);
        }
    }
}

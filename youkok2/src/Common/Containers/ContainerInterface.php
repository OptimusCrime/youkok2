<?php
namespace Youkok\Common\Containers;

use DI\Container;

interface ContainerInterface {
    public static function load(Container $container): void;
}

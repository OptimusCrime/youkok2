<?php
namespace Youkok\Common\Containers;

use Psr\Container\ContainerInterface;

interface ContainersInterface
{
    public static function load(ContainerInterface $container): void;
}

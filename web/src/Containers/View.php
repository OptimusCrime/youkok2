<?php
namespace Youkok\Containers;

use \Slim\Container;
use \Slim\Views\Twig;
use \Slim\Views\TwigExtension;

use Youkok\TwigPlugins\YoukokTwigExtension;

class View
{
    public static function load(Container $container)
    {
        $baseDir = $container->get('settings')['base_dir'];

        $container['view'] = function ($container) use ($baseDir) {
            $view = new Twig($baseDir . '/templates/', [
                'cache' => false,
                'auto_reload' => true,
                'debug' => true
            ]);

            // Instantiate and add Slim specific extension
            $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
            $view->addExtension(new TwigExtension($container['router'], $basePath));
            $view->addExtension(new YoukokTwigExtension($container['router'], $container['request']));

            return $view;
        };
    }
}

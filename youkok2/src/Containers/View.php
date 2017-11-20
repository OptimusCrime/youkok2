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
        $cacheDir = $container->get('settings')['cache_directory'];

        $container['view'] = function ($container) use ($baseDir, $cacheDir) {
            $cache = false;
            if (getenv('DEV') === '0') {
                $twigCacheDir = $cacheDir . 'twig';
                if (!file_exists($twigCacheDir)) {
                    mkdir($twigCacheDir);
                    if (file_exists($twigCacheDir) and is_writable($twigCacheDir)) {
                        $cache = $twigCacheDir;
                    }
                }
            }

            $view = new Twig($baseDir . '/templates/', [
                'cache' => $cache,
                'auto_reload' => getenv('DEV') === '1',
                'debug' => getenv('DEV') === '1'
            ]);

            // Instantiate and add Slim specific extension
            $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
            $view->addExtension(new TwigExtension($container['router'], $basePath));
            $view->addExtension(new YoukokTwigExtension($container['router'], $container['request']));

            return $view;
        };
    }
}

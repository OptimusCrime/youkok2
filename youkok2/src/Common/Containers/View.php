<?php
namespace Youkok\Common\Containers;

use Psr\Container\ContainerInterface;
use Slim\Views\TwigExtension;
use Slim\Views\Twig;

use Youkok\Web\TwigPlugins\YoukokTwigExtension;

class View implements ContainersInterface
{
    public static function load(ContainerInterface $container): void
    {
        $container['view'] = function (ContainerInterface $container): Twig {
            $request = $container->get('request');
            $router = $container->get('router');

            $templatesDir = getenv('TEMPLATE_DIRECTORY');
            $cacheDir = getenv('CACHE_DIRECTORY');

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

            $view = new Twig($templatesDir, [
                'cache' => $cache,
                'auto_reload' => getenv('DEV') === '1',
                'debug' => 1
            ]);

            // Instantiate and add Slim specific extension
            $basePath = rtrim(str_ireplace('index.php', '', $request->getUri()->getBasePath()), '/');
            $view->addExtension(new TwigExtension($router, $basePath));
            $view->addExtension(new YoukokTwigExtension($router, $request));

            return $view;
        };
    }
}

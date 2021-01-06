<?php
namespace Youkok\Common\Containers;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Interfaces\RouterInterface;
use Slim\Views\TwigExtension;
use Slim\Views\Twig;

use Youkok\Helpers\Configuration\Configuration;
use Youkok\Web\TwigPlugins\YoukokTwigExtension;

class View implements ContainersInterface
{
    public static function load(ContainerInterface $container): void
    {
        $container['view'] = function (ContainerInterface $container): Twig {
            /** @var Request $request */
            $request = $container->get('request');

            /** @var RouterInterface $router */
            $router = $container->get('router');

            $configuration = Configuration::getInstance();

            $templatesDir = $configuration->getDirectoryTemplate();
            $cacheDir = $configuration->getDirectoryCache();

            $cache = false;
            if (!$configuration->isDev()) {
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
                'auto_reload' => $configuration->isDev(),
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
